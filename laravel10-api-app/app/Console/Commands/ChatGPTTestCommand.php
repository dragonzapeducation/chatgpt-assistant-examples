<?php

namespace App\Console\Commands;

use App\Assistants\BossyBettyAssistant;
use Illuminate\Console\Command;

class ChatGPTTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:chat-g-p-t-test-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the chatgpt application';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        /**
         * See the app/Assistants/BossyBettyAssistant.php file to see Bossy Betties ChatGPT assistant ID and functions
         */
        $bossyBetty = new BossyBettyAssistant();
        $conversation = $bossyBetty->newConversation();
        $conversation->sendMessage('How difficult is it to build a spaceship tell me the steps?');

        // Block and wait until ChatGPT replies.. Can be problematic to use this function
        // outside of an API or console due to the blocking delay leading to a poor user experience
        // as they wait for the page to load. Recommended to be used in API's only
        $conversation->blockUntilResponded();

        print_r($conversation->getResponseData());

        // Gets the ChatGPT text response
        //   $conversation->getResponseData()->getResponse();
        // Gets the function call response
        //$conversation->getResponseData()->getFunctionCalls();



        /**
         * Response output
         * 
         * response = The text response from ChatGPT
         * function_calls = A function call trace of all the functions/actions that ChatGPT called, includes all the parameters and responses that ChatGPT passed and received.
         */

        //  php artisan app:chat-g-p-t-test-command
        // Dragonzap\OpenAI\ChatGPT\ResponseData Object
        // (
        //     [response:protected] => Listen up! Building a spaceship is no child's play, and I'm going to walk you through the steps because clearly, I need to ensure you understand what you're getting into. Here's what you need to do:

        // 1. **Education and Research**: First things first, educate yourself! You can't just start building without knowing the fundamentals of aerospace engineering, physics, and thermodynamics. Get your nose into books and research papers. And make it snappy!

        // 2. **Design**: Don't even think about winging it. You need a detailed design of the spacecraft. What's your mission? Satellites, humans, cargo? Decide and design every single system down to the last bolt. And yes, this includes propulsion, life support, structural integrity, power systems, and heat shielding. Do it meticulously!

        // 3. **Regulatory Approval**: You better not skip this! Get all necessary approvals and comply with international space law. It's not the Wild West up there. You'll need to deal with agencies like NASA or ESA, and that's just for starters.

        // 4. **Funding**: Money doesn't grow on trees. Find funding for your project. Investors, government grants, or maybe your own deep pockets if you've got them. Understand this clearly: without funding, you’re grounded.

        // 5. **Suppliers and Partnerships**: You can't do this alone. Establish relationships with suppliers for high-quality materials and components. Consider partnerships with established aerospace organizations. Network like your life depends on it!

        // 6. **Manufacturing**: Get to work on building the thing. And I mean top-quality manufacturing. This isn't a soapbox derby. Precision is key, so pay attention to detail. I want you to double-check everything!

        // 7. **Testing**: Test, and then test some more. Test every system to its breaking point. You think it's ready? Test it again! Space is unforgiving, and your systems need to work perfectly. Don't come crying to me if something breaks down up there because you skimped on testing.

        // 8. **Launch Site**: Secure a launch site. That's right, you can't just launch a rocket from your backyard. Coordinate with spaceports and ensure your launch infrastructure is all set.

        // 9. **Assembly and Integration**: Assemble the spacecraft components with the utmost care. One mistake can lead to a catastrophic failure. This is where you dot the i's and cross the t's.

        // 10. **Launch Preparation and Execution**: You're almost there. Prepare your spaceship for launch, go through your pre-launch checklist a thousand times if necessary. Once you're sure you've checked everything, you can finally launch. But stay vigilant!

        // 11. **Mission Execution**: If you've managed to launch it, the mission is only just beginning. Monitor and manage your spaceship throughout its voyage. Be prepared to deal with any issue, no matter how small.

        // 12. **Analysis and Debrief**: Post-mission – you better analyze every bit of data. What went well? What didn't? Learn and improve for the next time.

        // Remember, I don't tolerate excuses. You wanted to know about building a spaceship, and now you’ve got your orders. Follow these steps meticulously, and you may just pull it off. But I'll be watching closely to make sure you don't mess up. Get moving!
        //     [function_calls:protected] => Array
        //         (
        //         )
        // )

    }
}
