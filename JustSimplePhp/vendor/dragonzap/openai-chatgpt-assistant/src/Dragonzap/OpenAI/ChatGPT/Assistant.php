<?php

namespace Dragonzap\OpenAI\ChatGPT;
use OpenAI;
use Exception;

/**
 * An abstract class representing an Assistant.
 * 
 * This class serves as a blueprint for creating various types of assistants.
 * Each assistant will have its own implementation of the handleFunction method.
 */
abstract class Assistant
{
    protected APIConfiguration $api_config;
    protected OpenAI\Client $client;
    public function __construct(APIConfiguration $api_config=NULL)
    {
        $this->api_config = $api_config;
        if ($this->api_config == NULL) {
            try {
                $this->api_key = new APIConfiguration(config('services.openai.key'));
            } catch (Exception $e) {
                throw new Exception('If you do not provide a ' . APIConfiguration::class . ' then you must be using this module within Laravel framework. Details:'  . $e->getMessage());
            }
        }

        $this->client = OpenAI::client($this->api_config->getApiKey());
    }

    public function getApiConfiguration() : APIConfiguration
    {
        return $this->api_config;
    }

    public function getOpenAIClient() : OpenAI\Client
    {
        return $this->client;
    }

    public function newConversation() : Conversation
    {
        $response = $this->client->threads()->create([]);
        return new Conversation($this, $response, null);
    }

    public function loadConversation(ConversationIdentificationData $conversation_id_data) : Conversation
    {
        $thread = $this->client->threads()->retrieve($conversation_id_data->getConversationId());
        $run = null;
        $run_id = $conversation_id_data->getRunId();
        if ($run_id)
        {
            $run = $this->client->threads()->runs()->retrieve($thread->id, $run_id);
        }
        return new Conversation($this, $thread, $run);
    }

    /**
     * 
     * The creator of an assistant should return the assistant ID here, generally this would be returned directly unless you plan
     * to pass the ID into a constructor of some kind.
     * @return string Returns the assistant ID for the assistant
     */
    public abstract function getAssistantId(): string;

    /**
     * Handles a specific function required by the assistant.
     * 
     * @param string $function The name of the function to handle.
     * @param array $arguments An array of arguments passed for the function
     * @return string|array The result or response of the handled function either as a string or an array
     */
    public abstract function handleFunction(string $function, array $arguments): string|array;


 

}
