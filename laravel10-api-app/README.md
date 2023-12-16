<p align="center"><a href="https://dragonzap.com" target="_blank"><img src="https://dragonzap.com/dist/images/logo/logo.png" width="200" alt="Dragon Zap Logo" /></a></p>

## ChatGPT Assistant Wrapper Weather Station Example App

This repository provides a simple PHP Laravel 10 Application for using the ChatGPT Assistant wrapper. These examples are designed to help you understand how to integrate and use the Dragon Zap ChatGPT Assistant library in various scenarios. You may clone this repository to use as a starting base or follow the installation guide below

<p align="center">
  <img src="https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel10-api-app/screenshot.jpg" alt="Screenshot example of the laravel application" />
</p>

## Using this Laravel 10 ChatGPT Weather Application example

### Step 1 - Clone the repository
```bash
git clone https://github.com/dragonzapeducation/chatgpt-assistant-examples.git
```

### Step 2 - Modify the .env file
You need to modify the [".env"](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel10-api-app/.env) file to update the key value pairs for your api keys and other configuration settings.
Open the [".env"](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel10-api-app/.env) hidden file inside the `"laravel10-api-app"` directory and update the `OPENAI_CHATGPT_KEY` to equal to your OPENAI key that you created at [platform.openai.com](https://platform.openai.com) . Next you need to update the `OPENWEATHERMAP_KEY` so that it is equal to your Open Weather Map API Key which gives us access to weather data. You can register for the api key here for free: [https://openweathermap.org/](https://openweathermap.org/) it should be noted that it can take a few hours for your Open weather map API key to be initialized.

You will also want to update the following environmental variables to point to your own ChatGPT assistants
```
BOSSYBETTYAASSISTANT_ID=asst_EeISm92FY4mtwNK3yth0wFVS
WEATHERASSISTANT_ID=asst_cWrXNjOWrfhacZSKzjrOvwKq
```

Only the `WEATHERASSISTANT_ID` has to be modified

### Step 3 - Updating your weather assistant on ChatGPT
You now need to update your Weather assistant at  [platform.openai.com](https://platform.openai.com) give the assistant the following instructions:
```
You are a weather assistant your job is to help find the weather for the user. Be professional and friendly.
```


#### Creating the get_weather function
Next you need to create the `get_weather` function inside of your GPT Assistant. ChatGPT will call your get_weather function within your [Weather Assistant Class](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel10-api-app/app/Assistants/WeatherAssistant.php) whenever it wishes to look up the weather for a particular function. Define the `get_weather` function now at  [platform.openai.com](https://platform.openai.com):
```
{
  "name": "get_weather",
  "description": "Determine weather in my location",
  "parameters": {
    "type": "object",
    "properties": {
      "location": {
        "type": "string",
        "description": "The city and state e.g. London"
      }
    },
    "required": [
      "location"
    ]
  }
}
```

#### Creating the handle_weather function
Also inside your OPENAI ChatGPT Assistant create the handle_weather function
```
{
  "name": "handle_weather",
  "description": "Called when we should handle the obtained weather. Should be called everytime weather is found ",
  "parameters": {
    "type": "object",
    "properties": {
      "location": {
        "type": "string",
        "description": "The city location"
      },
      "temperature": {
        "type": "number",
        "description": "The temperature that has been obtained"
      },
      "extra_notes": {
        "type": "string",
        "description": "A simple sentance giving an overview of the weather conditions"
      },
      "category": {
        "type": "string",
        "enum": [
          "raining",
          "sunny",
          "clouds",
          "snowing"
        ]
      }
    },
    "required": [
      "location",
      "temperature",
      "extra_notes",
      "category"
    ]
  }
}
```
ChatGPT will call the `handle_weather` function in our [Weather Assistant Class](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel10-api-app/app/Assistants/WeatherAssistant.php) whenever it has obtained weather data. This will then allow our web application to add the weather to the web page so the user can see it. 

### Step 4 - Update composer
You need to run the `composer update` command inside the `laravel10-api-app` directory.


### Step 5 - Update composer
Ensure your `.env` file is saved and then run the `php artisan optimize:clear` command from within the `laravel10-api-app` directory.
### Step 6 - Run Artisan
You need to run artisan `php artisan serve --host=0.0.0.0` 

### Step 7 - Navigating to your web application
Go to a web browser and navigate to http://127.0.0.1:8000 where you will be able to talk with your weather assistant. 

## Custom Installation In A Seperate Project

You might want to install the ChatGPT library in your own laravel project, To install the [ChatGPT Assistant wrapper](https://github.com/dragonzapeducation/chatgpt-assistant), use Composer. Run the following command in your project directory:

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
### Weather Assistant
The weather assistant is controlled through the [AI Assistant Controller](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel10-api-app/app/Http/Controllers/Api/V1/AIAssistantController.php)

### Test Assistant Command
The Laravel command that tests the assistant is the [ChatGPTTestCommand.php](https://github.com/dragonzapeducation/chatgpt-assistant-examples/blob/main/laravel10-api-app/app/Console/Commands/ChatGPTTestCommand.php) file.

### Assistants
You can find all the ChatGPT assistants in the [App/Assistants](https://github.com/dragonzapeducation/chatgpt-assistant-examples/tree/main/laravel10-api-app/app/Assistants) namespace. 


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
