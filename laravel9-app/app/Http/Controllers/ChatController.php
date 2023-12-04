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
 * 
 *  * A crucial aspect managed by this controller is the tracking and updating of ChatGPT run states. Each ChatGPT conversation can be running/processing 
 * The controller ensures that the state of each run is accurately maintained and updated in the database using the line:
 * '$chatgptConversation->saved_state = $conversation->getIdentificationData()->getSaveDataString(); $chatgptConversation->save();'
 * This line is essential for preventing issues like duplicate completions, as it helps the application recognize when a particular run is finished or still in progress.
 * Proper management of these states is vital for avoiding redundancies and ensuring that conversations are correctly continued or concluded.
 * 
 * If you was to call the library with an older than present state then you may find duplicate messages from chatgpt appearing in your database.
 * 
 * The state should be updated when RunState is equal to RunState::COMPLETED and in cases where any new user messages are created.
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
    private function getAssistant($codename)
    {
        // Assistant ID below

        $assistant = new UnknownAssistant(NULL, 'asst_0q46BUiesPu5XStGHufJVCba');
        switch ($codename) {
            case 'sally':
                $assistant = new SallyAssistant();
                break;
        }

        return $assistant;
    }
    public function store(Request $request)
    {
        // We allow assistants of multiple types.. the assistant_codename contains the type we want.
        $chatgpt_assistant = $this->getAssistant($request->assistant_codename);
        $conversation = $chatgpt_assistant->newConversation();
        
        $save_data_string = $conversation->getIdentificationData()->getSaveDataString();
        $thread_id = $conversation->getIdentificationData()->getConversationId();

        $chatgpt_convo = new ChatGPTConversation();
        $chatgpt_convo->assistant_codename = $request->assistant_codename;
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
        $chatgpt_assistant = $this->getAssistant($chatgptConversation->assistant_codename);
        $chatgpt_assistant->setConversation($chatgptConversation);
        try {
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
        $chatgpt_assistant = $this->getAssistant($chatgptConversation->assistant_codename);
        $chatgpt_assistant->setConversation($chatgptConversation);

        try {
            $conversation = $chatgpt_assistant->loadConversation(ConversationIdentificationData::fromSaveData($chatgptConversation->saved_state));
        } catch (\Exception $ex) {
            // Here we just rethrow the loading of the exception if theirs an issue loading the conversation.
            // ideally you must handle this
            throw $ex;
        }
        try {
            $conversation->sendMessage($message);

            // Remember to update the save data again, doing this will allow us to retrieve from the
            // most up to date state.
            $save_data_string = $conversation->getIdentificationData()->getSaveDataString();
            $chatgptConversation->saved_state = $save_data_string;
            $chatgptConversation->save();
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
