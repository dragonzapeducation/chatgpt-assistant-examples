<?php

namespace App\Assistants;

use App\Models\ChatGPTConversation;
use Dragonzap\OpenAI\ChatGPT\Assistant;
use Dragonzap\OpenAI\ChatGPT\ConversationIdentificationData;

/**
 * Sally assistant is a simple silly assistant designed to handle chatgpt function calls to the get_weather function
 */
class SallyAssistant extends Assistant
{

    // Our laravel model for chatgpt conversations
    protected ChatGPTConversation|null $conversation;
    public function __construct(ChatGPTConversation $conversation=NULL)
    {
        parent::__construct(NULL);
        $this->conversation = $conversation;
    }

    public function setConversation(ChatGPTConversation $conversation): void
    {
        $this->conversation = $conversation;
    }

    /**
     * 
     * The creator of an assistant should return the assistant ID here, generally this would be returned directly unless you plan
     * to pass the ID into a constructor of some kind.
     * @return string Returns the assistant ID for the assistant
     */
    public function getAssistantId(): string
    {
        return 'asst_0q46BUiesPu5XStGHufJVCba';
    }

      /**
     * Invoked automaticallhy everytime the system expects us to save the conversation identification information
     * We must save the save data to the model so that the current conversation state can be restored. Failure to do so
     * will result in an imbalanced state which could lead the system to re-reading old messages.
     */
    public function saveConversationIdentificationData(ConversationIdentificationData $conversation_id_data): void
    {
        // By saving the chatgpt conversation identification data string into our local chatgpt model
        // it allows us to reload the conversation at its exact state at a later point in the future
        if (!$this->conversation)
        {
            // We don't know about the conversation yet so we will just leave
            return;
        }

        // The save data string is used to reload conversations, it must be accurate for the system to function
        // correctly. Whenever this function is called we must make efforts to save this data string.
        $this->conversation->saved_state = $conversation_id_data->getSaveDataString();
        $this->conversation->save();
    }

    private function handleGetWeatherFunction(array $arguments)
    {
        $success = false;
        $message = 'We could not locate the weather for ' . $arguments['location'] . ' as it is not in our database';

        switch (strtolower($arguments['location'])) {
            case 'cardiff':
                $success = true;
                $message = 'The weather in wales, cardiff is Rainy today';
                break;

            case 'london':
                $success = false;
                $message = 'As usual england is freezing';
                break;

            case 'perth':
                $success = false;
                $message = 'Australia, Perth is very hot at 45 Celcius everyone is cooking';
                break;
        }
        return [
            'success' => $success,
            'message' => $message,
        ];
    }


    
    /**
     * Handles a specific function required by the assistant.
     * 
     * @param string $function The name of the function to handle.
     * @param array $arguments An array of arguments passed for the function
     * @return string|array The result or response of the handled function either as a string or an array
     */

    public function handleFunction(string $function, array $arguments): string|array
    {
        $response = [];

        switch ($function) {
            case 'get_weather':
                $response = $this->handleGetWeatherFunction($arguments);
                break;

            default:
                $response = [
                    'success' => false,
                    'message' => 'Unknown function'
                ];
        }
        return $response;
    }



}

