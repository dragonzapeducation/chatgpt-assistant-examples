<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Assistant</title>
    <!-- Add your stylesheet links here -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <a href="https://dragonzap.com"><img src="{{asset('logo.png')}}" /></a>
<br />
<br />
    <div id="messageList" style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
        <!-- Messages will be displayed here -->
        @foreach($chatgptConversation->messages()->orderBy('created_at', 'asc')->get() as $message)
        {{$message->from}}:{{$message->content}} <br />
        @endforeach
    </div>

    <form action="{{ route('chats.message.send', $chatgptConversation) }}" method="POST">
        @csrf
        
        <textarea id="messageBox" name="message" rows="3" style="width: 100%;"></textarea>
        <button id="sendMessage">Send</button>

        <p><b>Response Status:</b> <span class="status-message">non_existant</span></p>
    </form>

    @if($chatgptConversation->assistant_codename == 'sally')
    <p>As your using the Sally assistant you can ask about the weather, say "What is the weather in cardiff?"
        Take a look at the app/Assistants/SallyAssistant.php file to see this functionality in action</p>
    @endif

    <script>
        $(document).ready(function() {

            setInterval(() => {
                pollServer();
            }, 5000);

            function pollServer() {
                $.ajax({
                    url: '{{ route('chats.poll', $chatgptConversation) }}', // Your poll URL
                    type: 'GET', // or POST, depending on your server setup
                    success: function(response) {
                        $('.status-message').html(response.run_state);
                        if (response.new_response) {
                            // If there is a new response, display it
                            $('#messageList').append('<div>Assistant: ' + response.assistant_response +
                                '</div>');
                            // You might want to refresh the page or update the conversation in another way
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>
