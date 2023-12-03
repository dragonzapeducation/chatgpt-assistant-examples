<?php

namespace Dragonzap\OpenAI\ChatGPT;

use Dragonzap\OpenAI\ChatGPT\Exceptions\IncompleteRunException;
use Dragonzap\OpenAI\ChatGPT\Exceptions\UnsupportedRunException;
use OpenAI;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use OpenAI\Responses\Threads\ThreadResponse;
use Exception;


enum RunState: string
{
    // NON_EXISTANT status means that the conversation has not called run() yet or that a previous run() has been handled already
    case NON_EXISTANT = 'non_existant';
    case QUEUED = 'queued';

    case RUNNING = 'running';
    case COMPLETED = 'completed';

    // INVOKING_FUNCTION is returned when chatgpt wants to call a function defined in your assistant.
    // We set this when we are aware and will attempt to invoke this action on your behalf.
    // You do not have to do anything with this.
    case INVOKING_FUNCTION = 'invoking_function';
    case FAILED = 'failed';

    case UNKNOWN = 'unknown';
}

/**
 * Represents a conversation
 */
class Conversation
{
    protected Assistant $assistant;
    protected ThreadResponse $thread;

    protected ThreadRunResponse|null $current_run;

    public function __construct(Assistant $assistant, ThreadResponse $thread, ThreadRunResponse|null $current_run)
    {
        $this->assistant = $assistant;
        $this->thread = $thread;
        $this->current_run = $current_run;
    }

    /**
     * @return ConversationIdentificationData Returns an object which identifies the current conversation
     */
    public function getIdentificationData() : ConversationIdentificationData
    {
        $thread_id = $this->thread->id;
        $run_id = null;
        if ($this->current_run)
        {
            $run_id = $this->current_run->id;
        }
        return new ConversationIdentificationData($thread_id, $run_id);
    }

    public function sendMessage(string $message, string $role = 'user', bool $autorun = true): void
    {
        $this->assistant->getOpenAIClient()->threads()->messages()->create($this->thread->id, [
            'role' => $role,
            'content' => $message,
        ]);
        if ($autorun) {
            $this->run();
        }
    }

    public function run(): void
    {
        $this->current_run = $this->assistant->getOpenAIClient()->threads()->runs()->create(
            threadId: $this->thread->id,
            parameters: [
                'assistant_id' => $this->assistant->getAssistantId(),
            ],
        );

    }

    public function getThreadResponse(): ThreadResponse
    {
        return $this->thread;
    }

    private function getRunStateFromOpenAIRunState(string $state): RunState
    {
        $run_state = RunState::UNKNOWN;

        switch ($state) {
            case 'queued':
                $run_state = RunState::QUEUED;
                break;

            case 'in_progress':
                $run_state = RunState::RUNNING;
                break;

            case 'completed':
                $run_state = RunState::COMPLETED;
                break;

            case 'requires_action':
                // We will automatically invoke the function later, so mark it as invoking function
                $run_state = RunState::INVOKING_FUNCTION;
                break;

            case 'failed':
            case 'expired':
            case 'cancelled':
                $run_state = RunState::FAILED;
                break;
        }

        return $run_state;
    }

    public function getResponse(): string
    {
        if ($this->current_run->status != 'completed') {
            throw new IncompleteRunException('The status of the job is not yet completed. Run Conversation::getRunState() to refresh the cache of this current run if it returns RunState::COMPLETED you will be able to get a response by calling this function again');
        }

        $response = $this->assistant->getOpenAIClient()->threads()->messages()->list($this->thread->id, [
            'limit' => 1,
        ]);


        return $response->data[0]->content[0]->text->value;
    }

    /**
     * Blocks the execution until ChatGPT responds to a message or there was a failure of some kind
     * 
     * Warning: Ideally should only be used in API's or console applications, avoid use if possible as long timeouts
     * disrupt user experience and strain the web server.
     */
    public function blockUntilResponded(): RunState
    {
        $run_state = $this->getRunState();
        while ($run_state != RunState::COMPLETED && $run_state != RunState::FAILED) {
            sleep(1);
            $run_state = $this->getRunState();
        }

        return $run_state;
    }

    private function handleRequiresAction()
    {
        // We dont support action types that are not of submit_tool_outputs
        if ($this->current_run->requiredAction->type != 'submit_tool_outputs') {
            throw new UnsupportedRunException('The library does not yet handle action types of ' . $this->current_run->requiredAction->type);
        }

        $action_function_tool_calls = $this->current_run->requiredAction->submitToolOutputs->toolCalls;
        $tool_outputs = [];

        foreach ($action_function_tool_calls as $action_function_tool_call) {
            if ($action_function_tool_call->type != 'function') {
                throw new UnsupportedRunException('The library does not yet handle functions that are not of type function');
            }
            $tool_call_id = $action_function_tool_call->id;
            $function_name = $action_function_tool_call->function->name;
            $function_arguments = json_decode($action_function_tool_call->function->arguments, true);
            $function_response = $this->assistant->handleFunction($function_name, $function_arguments);
            if (is_array($function_response)) {
                // By default we JSON encode for arrays and paass back only strings.
                $function_response = json_encode($function_response);
            }

            $tool_outputs[] = ['tool_call_id' => $tool_call_id, 'output' => $function_response];
        }

        // Now we have called the function and got a response lets pass it back to chatgpt
        $this->current_run = $this->assistant->getOpenAIClient()->threads()->runs()->submitToolOutputs(
            threadId: $this->thread->id,
            runId: $this->current_run->id,
            parameters: [
                'tool_outputs' => $tool_outputs,
            ]
        );

    }
    public function getRunState(): RunState
    {
        if (!$this->current_run) {
            return RunState::NON_EXISTANT;
        }

        $this->current_run = $this->assistant->getOpenAIClient()->threads()->runs()->retrieve(
            threadId: $this->thread->id,
            runId: $this->current_run->id,
        );


        if ($this->current_run->status == 'requires_action') {
            $this->handleRequiresAction();
        }

        return $this->getRunStateFromOpenAIRunState($this->current_run->status);
    }

}
