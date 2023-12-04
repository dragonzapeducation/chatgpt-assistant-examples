# ChatGPT Assistant Wrapper Examples in PHP

This repository provides simple PHP examples for using the ChatGPT Assistant wrapper. These examples are designed to help you understand how to integrate and use the ChatGPT Assistant in various scenarios.

## Installation

To install the ChatGPT Assistant wrapper, use Composer. Run the following command in your project directory:

```bash
composer require dragonzap/openai-chatgpt-assistant
```
After package installation, register the service provider in your Laravel application.

1. Open your `config/app.php` file and locate the `providers` array.
2. Add the `ChatGptAssistantProvider` to the array:
    ```php
    'providers' => [
        // Other Service Providers

        Dragonzap\OpenAI\ChatGPT\ChatGptAssistantProvider::class,
    ],
    ```

This will enable the Laravel application to recognize and utilize the service provider.

Finally you will need to publish the configuration file
```bash
php artisan vendor:publish --provider="Dragonzap\OpenAI\ChatGPT\ChatGptAssistantProvider" --force
```

Now you should find a new file named `config/dragonzap.php` open it up where you will find the configuration
```php
<?php

return [
    'openai' => [
        'key' => env('OPENAI_CHATGPT_KEY', 'default-key-value')
    ]
];
```
You can modify your `.env` file to include your `OPENAI_CHATGPT_KEY` thus completing the installation.


## Laravel Examples
### Sally Assistant
Sally the weather assistant will provide weather insights, [Sally Weather Assistant Source file](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel9-app/app/Assistants/SallyAssistant.php) , Sally Assistant is used by the [Chat Controller](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel9-app/app/Http/Controllers/ChatController.php)

### Blocking example command
Learn how to use the chatgpt assistant in a blocking manner which is the easiest to implement here: https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel9-app/app/Console/Commands/DragonZapChatgptConversationTestCommand.php

## Other Examples

### 1. Console Blocking Chat Example
This example demonstrates a console-based chat application using the ChatGPT Assistant wrapper.  
**Code:** [Console Chat Example](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/JustSimplePhp/src/console-chat-example.php)

### 2. Simple Usage of Assistants
A basic example showing how to use assistants in a straightforward manner.  
**Code:** [Simple Assistant Example](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/JustSimplePhp/src/unknown-assistant-example.php)

### 3. Assistants in Web Applications
For integrating assistants in web applications, this example will guide you through.  
**Code:** [Web Application Assistant Example](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/JustSimplePhp/src/reload-conversation-example.php)

### 4. Direct Access to OpenAI API
If you need to access the OpenAI API directly, bypassing the `dragonzap/openai-chatgpt-assistant` library, check out this example.  
**Code:** [OpenAI Direct API Example](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/JustSimplePhp/src/openai-direct-api-example.php)

---

Feel free to explore these examples to better understand how to work with the ChatGPT Assistant wrapper in PHP.
