<?php
namespace App\Assistants;
use Dragonzap\OpenAI\ChatGPT\Assistant;
use Dragonzap\OpenAI\ChatGPT\ConversationIdentificationData;

class BossyBettyAssistant extends Assistant
{
    public function getAssistantId(): string
    {
        return 'asst_EeISm92FY4mtwNK3yth0wFVS';
    }
    public function handleFunction(string $function, array $arguments): array|string
    {
        return [
            'success' => 'false',
            'message' => 'No functions implemented yet'
        ];
    }

    public function saveConversationIdentificationData(ConversationIdentificationData $conversation_id_data) : void
    {
        // Not implemented.
    }
}