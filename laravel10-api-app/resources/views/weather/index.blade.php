<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} - Weatherforecast</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');


        .chat .card {
            width: 500px;
            border: none;
            border-radius: 15px;
        }

        .adiv {
            border-radius: 15px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
            font-size: 19px;
            height: 56px;
        }

        .chat {
            border: none;
            background: #3fa0ff;
            color: white;
            font-size: 18px;
            border-radius: 20px;

        }

        .chat-card .myvideo img {
            border-radius: 20px
        }

        .chat-card .dot {
            font-weight: bold;
            font-size: 20px;
            /* Adjust the font size to change the size of the dots */
            letter-spacing: 5px;
            /* Adjust space between the dots */
            display: inline-block;
            /* Needed to apply spacing and size */
        }

        .chat-card .form-control {
            border-radius: 12px;
            border: 1px solid #F0F0F0;
            font-size: 18px;
            resize: none;
            /* To prevent resizing */
        }

        .chat-card .form-control:focus {
            box-shadow: none;
        }

        .chat-card .form-control::placeholder {
            font-size: 18px;
            color: #C4C4C4;
        }

        .chat-card .send-btn {
            background-color: white;
            /* Changed to white */
            color: #1089ff;
            /* Text/icon color - can adjust as needed */
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-left: 10px;
            display: flex;
            justify-content: center;
            align-items: center;

            padding: 0 5px;
            /* Adjust as needed */
            cursor: pointer;
            /* Indicates the button is clickable */
        }

        .chat-card .send-btn:hover {
            background-color: #e6e6e6;
            /* Slightly darker or lighter on hover */
        }

        .chat-card .form-control,
        .chat-card .send-btn {
            align-self: stretch;
            /* Stretches to fill the available height */
        }

        .chat-card .send-btn svg {
            fill: #1089ff;
            /* SVG icon color - adjust as needed */
        }

        .chat-card .send-btn i {
            font-size: 16px;
        }

        .chat,
        .myvideo img,
        .chat-card .form-control,
        .chat-card .send-btn {
            border-radius: 15px;
            /* Example: 15px, adjust as needed */
        }

        /* Placeholder text contrast */
        .chat-card .form-control::placeholder {
            color: #a9a9a9;
            /* Adjust for better contrast */
        }

        /* Scrollable chat area */
        .chat-messages {
            overflow-y: auto;
            height: 450px;
            /* Allows scrolling */
            max-height: 450px;
            /* Adjust as needed */
        }

        .typing-indicator {
            display: inline-block;
            margin-left: 5px;
        }

        .typing-indicator span {
            display: inline-block;
            opacity: 0;
            animation: dot 1.5s infinite;
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: 0s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.25s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.5s;
        }

        @keyframes dot {
            0% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }



        /* Responsive adjustments */
        @media (max-width: 576px) {
            .chat-card {
                width: 100%;
            }
        }


        .weather-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 10px 0;
            padding: 20px;
            box-sizing: border-box;
        }


        .weather-card-header {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .weather-card-body {
            font-size: 16px;
            color: #666;
        }

        .city-name {
            display: block;
            margin-bottom: 10px;
        }

        .temperature {
            margin: 0;
        }

        .weather-description {
            margin: 0;
        }

        .weather-content {
            flex-grow: 1;
        }

        .weather-icon {
            flex-shrink: 0;
            padding-left: 20px;
            /* Space between content and icon */
        }
    </style>

</head>

<body>

    <div class="container">
        <div class="px-4 py-5 my-5 text-center">
            <a href="https://dragonzap.com"><img class="d-block mx-auto mb-4"
                    src="https://dragonzap.com/dist/images/logo/logo.png" alt=""></a>

            <h1 class="display-5 fw-bold">Weather Assistant</h1>
            <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">Quickly get the current weather in any location in the world, even compare multiple
                    cities just chat below</p>

            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card  chat-card">
                    <div class="d-flex flex-row justify-content-between p-3 adiv text-white bg-primary">
                        <i class="fas fa-chevron-left"></i>
                        <span class="pb-3">Live chat: Weather Assistant</span>
                        <i class=""></i>
                    </div>
                    <div class="chat-messages">
                        <div class="d-flex flex-row p-3">
                            <img src="{{ asset('images/chat/icons/circled-user-female.png') }}" width="30"
                                height="30">
                            <div class="chat ml-2 p-3">I'm a helpful weather assistant ask me for the weather in a
                                particular city?</div>
                        </div>



                    </div>
                    <div class="d-flex flex-row p-3">
                        <div class="typing-indicator-box">
                            <img src="{{ asset('images/chat/icons/circled-user-female.png') }}" width="30"
                                height="30">
                            <div class="typing-indicator">
                                <span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex flex-row align-items-center form-group px-3">
                        <input type="text" class="form-control" placeholder="Type your message" />
                        <button class="btn send-btn"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                    </div>
                    <div class="pt-1"></div>
                </div>
            </div>

            <div class="col-md-6 weather-data">

            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <p class="text-muted" style="text-align: center">Licensed under GPlv2 open sourced by <a
                            href="https://dragonzap.com">Dragon Zap Education</a></p>
                </div>

            </div>
        </div>


</body>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
</script>

<script>
    $(document).ready(function() {
        $('.typing-indicator-box').hide();



        var saved_state = null;

        function scrollToBottom() {
            var chatMessages = $('.chat-messages');
            chatMessages.scrollTop(chatMessages.prop("scrollHeight"));
        }

        function applyWeatherCard(response) {
            // Assuming response contains the necessary weather data
            var allWeatherData = response.calls.filter(call => call.function_name === "handle_weather");
            allWeatherData.forEach(weatherData => {
                // Extracting weather data
                var city = weatherData.function_arguments.location;
                var temperature = weatherData.function_arguments.temperature;
                var weatherDescription = weatherData.function_arguments.extra_notes;
                var iconUrl = weatherData.response
                    .icon_url; // Ensure this corresponds to your actual data structure

                // Creating weather card HTML
                var weatherCardHtml = '<div class="weather-card">' +
                    '<div class="weather-content">' +
                    '<div class="weather-card-header">' +
                    '<span class="city-name">' + city + '</span>' +
                    '</div>' +
                    '<div class="weather-card-body">' +
                    '<p class="temperature">Temperature: ' + temperature + '°C</p>' +
                    '<p class="weather-description">' + weatherDescription + '</p>' +
                    '</div>' +
                    '</div>' +
                    '<div class="weather-icon">' +
                    '<img src="' + iconUrl + '" alt="Weather Icon" />' +
                    '</div>' +
                    '</div>';

                // Append the weather card to the chat interface
                $('.weather-data').append(weatherCardHtml);
            });
        }

        function sendMessage() {
            var userInput = $('.form-control').val().trim();

            // Check if there's any user input
            if (userInput === '') {
                alert('Please type a message.');
                return;
            }

            // Append user's message to the chat box
            var userMessageHtml = '<div class="d-flex flex-row p-3">' +
                '<div class="bg-white mr-2 p-3"><span>' + userInput + '</span></div>' +
                '<img src="{{ asset('images/chat/icons/circled-user-male.png') }}" width="30" height="30">' +
                '</div>';
            $('.chat-card .chat-messages').append(userMessageHtml);

            scrollToBottom();
            $('.typing-indicator-box').show();

            // Prepare the data for the API call
            var requestData = {
                question: userInput,
                saved_state: saved_state // Replace with actual saved state if you have one
            };

            // AJAX request using jQuery
            $.ajax({
                url: "{{ route('api.v1.assistants.askQuestion', ['assistant' => 'weather']) }}",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(requestData),
                success: function(response) {
                    if (response.success) {
                        // Append response to the chat box
                        var replyText = response.response_data.response;
                        var replyHtml = '<div class="d-flex flex-row p-3">' +
                            '<img src="{{ asset('images/chat/icons/circled-user-female.png') }}" width="30" height="30">' +
                            '<div class="chat ml-2 p-3">' + replyText + '</div>' +
                            '</div>';
                        $('.chat-card .chat-messages').append(replyHtml);
                        $('.typing-indicator-box').hide();

                        applyWeatherCard(response.response_data);
                        scrollToBottom();
                        saved_state = response.saved_state;
                    } else {
                        // Handle error
                        console.error("API call unsuccessful.");
                        $('.typing-indicator-box').hide();
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX error
                    console.error("AJAX request failed: " + error);
                    $('.typing-indicator-box').hide();
                }
            });

            // Clear the input field
            $('.form-control').val('');
        }

        $('.send-btn').click(function() {
            sendMessage();
        });

        $('.form-control').keypress(function(e) {
            if (e.which == 13) { // Enter key pressed
                sendMessage();
                e.preventDefault(); // Prevent default action (new line)
            }
        });
    });
</script>

</html>
