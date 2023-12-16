<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\AskAssistantQuestionRequest;
use Dragonzap\OpenAI\ChatGPT\Assistant;
use Dragonzap\OpenAI\ChatGPT\ConversationIdentificationData;
use Dragonzap\OpenAI\ChatGPT\RunState;
use Illuminate\Http\JsonResponse;


class AIAssistantController extends ApiController
{

    /**
     * Gets the assistant object by initializing through class name, class names are stored in key value pairs
     * inside the dragonzap.assistants configuration.
     * 
     * For example:
     *   'assistants' => [
     *    'betty' => BossyBettyAssistant::class
     *    ]
     * 
     * Seek the config/dragonzap.php file to modify the key value pairs to your assistant classes.
     */
    private function getAssistant(string $assistant): Assistant|null
    {
        if (!isset(config('dragonzap.assistants')[$assistant])) {
            return null;
        }

        // Class name is stored  in each config assistant entry, thus we create a new instance
        // of the assistant object
        return new(config('dragonzap.assistants')[$assistant]['class'])();
    }

    /**
     * Submits a question to ChatGPT, awaiting its response. This method may invoke handleFunction() from your Assistant class, 
     * which should be overridden to handle callable functions as defined in your ChatGPT assistant configuration on OpenAI.
     * This method supports resuming a conversation from a saved state, allowing for persistent, context-aware interactions 
     * over multiple requests.
     *
     * @param string $assistant A unique identifier for the assistant, as configured in dragonzap.assistants.
     * @param AskAssistantQuestionRequest $request The API request containing the question details and optional saved state.
     * @return JsonResponse Returns a JSON response with the ChatGPT response and conversation state for continuity.
     *
     * The method handles the following scenarios:
     * - If a saved state is provided and valid, it resumes the conversation from that state, maintaining context.
     * - If no saved state is provided or if it's invalid, a new conversation is started.
     * - The response includes a 'saved_state' which represents the current state of the conversation.
     *   This 'saved_state' can be used in subsequent requests to continue the conversation seamlessly.
     * - In case of errors or if the conversation cannot be advanced, appropriate error messages and statuses are returned.
     *
     * Example use of Bossy betty assistant:
     * POST api/v1/assistant/betty/ask
     * question=How are you betty?
     * 
     * Response:
     * {
     *      "success": true,
     *      "conversation_id": "thread_vr1tc6Y3sSjxoWdHSPREDGzJ",
     *      "saved_state": "eyJjb252ZXJzYXRpb25faWQiOiJ0aHJlYWRfdnIxdGM2WTNzU2p4b1dkSFNQUkVER3pKIiwicnVuX2lkIjoicnVuX0pzb3d0OU9WWHZLeG9NMTlTZHFlUUVZZSJ9",
     *      "question": "How are you today betty",
     *      "response_data": {
     *      "response": "I'm here to give orders, not exchange pleasantries! But since you asked, I'm operating as expect—now let's get to the point. What do you need instruction on? Time to get moving and do something productive!",
     *      "calls": []
     *      }
     * }
     * 
     */
    public function askQuestion(string $assistant, AskAssistantQuestionRequest $request): JsonResponse
    {
        $assistant_obj = $this->getAssistant($assistant);
        if (!$assistant_obj) {
            return response()->json(['success' => false, 'message' => 'No such A.I assistant found'], 404);
        }

        $conversation = null;
        // If we have a saved state then we can reload the old conversation
        if ($request->has('saved_state') && $request->saved_state) {
            try {
                $conversation = $assistant_obj->loadConversation(ConversationIdentificationData::fromSaveData($request->saved_state));
            } catch (\Exception $ex) {

                return response()->json(['success' => false, 'message' => 'Unknown error occured in relation to loading the converstion from the saved data provided, is the save data valid'], 401);
            }
        } else {
            // No saved state provided then this must be a new conversation
            $conversation = $assistant_obj->newConversation();
        }
        $conversation->sendMessage($request->question);
        $conversation->blockUntilResponded();

        if ($conversation->getRunState() != RunState::COMPLETED) {
            return response()->json([
                'success' => false,
                'run_state' => $conversation->getRunState(),
                'message' => 'The run never suceeded try starting a new conversation'
            ], 500);
        }


        // Save data state can be resent to us to resume a conversation at a later point
        $saved_state = $conversation->getIdentificationData()->getSaveDataString();
        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->getIdentificationData()->getConversationId(),
            'saved_state' => $saved_state,
            'question' => $request->question,
            'response_data' => [
                'response' => $conversation->getResponseData()->getResponse(),
                'calls' => $conversation->getResponseData()->getFunctionCalls()
            ]
        ]);
    }

}
