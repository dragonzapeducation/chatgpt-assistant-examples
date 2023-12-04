<?php

namespace App\Http\Controllers;

use App\Assistants\SallyAssistant;
use App\Models\ChatGPTConversation;
use App\Models\ChatGPTConversationMessage;
use Dragonzap\OpenAI\ChatGPT\ConversationIdentificationData;
use Dragonzap\OpenAI\ChatGPT\RunState;
use Dragonzap\OpenAI\ChatGPT\UnknownAssistant;
use Illuminate\Http\Request;

/**
 * This controller manages interactions with ChatGPT in a Laravel application.
 * Unlike the console command in the app/Console/Commands/DragonZapChatgptConversationTestCommand.php file, which was primarily for testing a simple ChatGPT conversation,
 * this controller integrates ChatGPT interactions within a web application context.
 *
 * It provides various functionalities:
 * - Indexing and viewing all ChatGPT conversations.
 * - Creating and managing ChatGPT conversations and their states.
 * - Polling for assistant responses and storing them in a database.
 * - Sending messages to ChatGPT and handling the responses.
 * 
 * The controller employs a method 'getAssistant' to initialize ChatGPT assistants of different types, 
 * thus allowing for flexibility in managing multiple assistant personalities or configurations.
 */

class ChatController extends Controller
{
    public function index()
    {
        $conversations = ChatGPTConversation::get();
        return view('chats', compact('conversations'));
    }


    /**
     * Used to allow you to create assistants of particular types
     */
    private function getAssistant($codename, ChatGPTConversation|null $chatgptConversation = null)
    {
        // Assistant ID below

        $assistant = null;
        switch ($codename) {
            case 'sally':
                $assistant = new SallyAssistant();
                $assistant->setConversation($chatgptConversation);
                break;
        
            default:
                throw new \Exception('Assistant not found');
        }

        return $assistant;
    }
    public function store(Request $request)
    {
        // We allow assistants of multiple types.. the assistant_codename contains the type we want.
        $chatgpt_assistant = $this->getAssistant($request->assistant_codename);
        $conversation = $chatgpt_assistant->newConversation();
        
        // You can think of the save data string as a way to restore the conversation
        // at a later point in time. Which is essential for our application since your able
        // to store chats in the database.
        $save_data_string = $conversation->getIdentificationData()->getSaveDataString();
        $thread_id = $conversation->getIdentificationData()->getConversationId();

        $chatgpt_convo = new ChatGPTConversation();
        $chatgpt_convo->assistant_codename = $request->assistant_codename;

        // Store the save data string of the libraries chatgpt conversation object, we can use it later to load
        // the conversation again.
        $chatgpt_convo->saved_state = $save_data_string;
        $chatgpt_convo->thread_id = $thread_id;
        $chatgpt_convo->save();

        return redirect()->route('chats.view', $chatgpt_convo);
    }

    /**
     * Called frequently via AJAX 
     * This poll is responsible for obtaining replies from the assistant and then storing them.
     */
    public function poll(ChatGPTConversation $chatgptConversation, Request $request)
    {

        // Get the assistant by the codename it was created with
        $chatgpt_assistant = $this->getAssistant($chatgptConversation->assistant_codename, $chatgptConversation);
      
        try {
            // Here we will reload the conversation from the saved_state which was stored when the conversation was created.
            // Any changes to the conversation will result in our chatgpt conversation model's saved_state to have to be updated again
            // thankfully this is handled by the Dragonzap chatgpt assistants library automatically. Take a look at the SallyAssistant
            // found here app/Assistants/SallyAssistant.php  . You will see the saveConversationIdentificationData method. This method
            // is called whenever we need to reupdate the saved_state in our chatgptConversation.
            $conversation = $chatgpt_assistant->loadConversation(ConversationIdentificationData::fromSaveData($chatgptConversation->saved_state));
        } catch (\Exception $ex) {
            // Here we just rethrow the exception if theirs an issue loading the conversation.
            // ideally you must handle this as chatgpt threads/conversations do expire
            throw $ex;
        }
        $run_state = $conversation->getRunState();
        if ($run_state == RunState::COMPLETED) {
            // Since the run is completed we can get the response and store the message
            $chatgpt_conversation_message = new ChatGPTConversationMessage();
            $chatgpt_conversation_message->conversation()->associate($chatgptConversation);
            $chatgpt_conversation_message->from = 'Assistant';
            $chatgpt_conversation_message->content = $conversation->getResponse();
            $chatgpt_conversation_message->save();


            return response()->json(['success' => true, 'run_state' => $run_state, 'new_response' => true, 'assistant_response' => $chatgpt_conversation_message->content]);
        }

        return response()->json(['success' => true, 'run_state' => $run_state, 'new_response' => false]);

    }

    public function sendMessage(Request $request, ChatGPTConversation $chatgptConversation)
    {
        $message = $request->get('message');

        // Get the assistant by the codename that it was created with
        $chatgpt_assistant = $this->getAssistant($chatgptConversation->assistant_codename, $chatgptConversation);

        try {
              // Here we will reload the conversation from the saved_state which was stored when the conversation was created.
            // Any changes to the conversation will result in our chatgpt conversation model's saved_state to have to be updated again
            // thankfully this is handled by the Dragonzap chatgpt assistants library automatically. Take a look at the SallyAssistant
            // found here app/Assistants/SallyAssistant.php  . You will see the saveConversationIdentificationData method. This method
            // is called whenever we need to reupdate the saved_state in our chatgptConversation.
            $conversation = $chatgpt_assistant->loadConversation(ConversationIdentificationData::fromSaveData($chatgptConversation->saved_state));
        } catch (\Exception $ex) {
            // Here we just rethrow the loading of the exception if theirs an issue loading the conversation.
            // ideally you must handle this
            throw $ex;
        }
        try {
            $conversation->sendMessage($message);
        } catch (\Exception $ex) {

            // In this test enviroment we just throw this, but you will want to handle it in real life senarios.
            throw $ex;
        }
        $chatgpt_conversation_message = new ChatGPTConversationMessage();
        $chatgpt_conversation_message->conversation()->associate($chatgptConversation);
        $chatgpt_conversation_message->from = 'User';
        $chatgpt_conversation_message->content = $message;
        $chatgpt_conversation_message->save();

        return back()->with('success', 'Message sent to chatgpt');

    }
    public function view(ChatGPTConversation $chatgptConversation)
    {
        return view('view', compact('chatgptConversation'));
    }
}
