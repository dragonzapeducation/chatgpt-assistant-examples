<?php

namespace App\Console\Commands;

use Dragonzap\OpenAI\ChatGPT\RunState;
use Dragonzap\OpenAI\ChatGPT\UnknownAssistant;
use Illuminate\Console\Command;

/**
 * This Laravel command facilitates testing a basic interaction with ChatGPT.
 * It demonstrates synchronous communication, where the execution waits for ChatGPT's response.
 * 
 * This approach can be integrated into an API. In such cases, it's recommended to implement rate limiting
 * to safeguard against potential denial of service attacks due to the blocking nature of the response mechanism.
 */
class DragonZapChatgptConversationTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dragonzap:chatgpt:conversation:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a simple chatgpt assistant conversation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Replace the asst_0q46BUiesPu5XStGHufJVCba with your own assistant ID.
        // The OpenAI API key is stored in config/dragonzap.php replace the "1234" with your own api key
        // You will need to first follow the installation guide in the README.md file of the laravel9 directory
        // For complete guide on using the  dragonzap chatgpt assistants/conversations library take a look at the raw PHP examples found here
        // : https://github.com/dragonzapeducation/chatgpt-assistant-examples/tree/main/JustSimplePhp
        $assistant = new UnknownAssistant(NULL, 'asst_0q46BUiesPu5XStGHufJVCba');
        $conversation = $assistant->newConversation();  
        $conversation->sendMessage('Hello world! how are you!');
        $conversation->blockUntilResponded();
        

        assert($conversation->getRunState() == RunState::COMPLETED);

        echo $conversation->getResponse() . "\n";
        return Command::SUCCESS;
    }
}
