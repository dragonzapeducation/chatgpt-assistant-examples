<?php

namespace Dragonzap\OpenAI\ChatGPT;
/**
 * A class representing a unknown assistant, you can extend this class for situations where
 * you dont really know about the type of assistant you will be using.
 */
class UnknownAssistant extends Assistant
{

    protected $assistant_id;
    public function __construct(APIConfiguration $api_config = NULL, string $assistant_id)
    {
        parent::__construct($api_config);
        $this->assistant_id = $assistant_id;
    }



    /**
     * To support functions you should override this method in a new implementation.
     */
    public function handleFunction(string $function, array $arguments): string|array
    {

        $response = [
            'success' => false,
            'message' => 'Functions are not supported for this unknown assistant'
        ];

        return $response;
    }
    /**
     * Returns the assistant ID for this unknown assistant.
     */
    public function getAssistantId(): string
    {
        return $this->assistant_id;
    }



}
