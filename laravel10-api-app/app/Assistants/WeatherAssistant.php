<?php
namespace App\Assistants;

use Cmfcmf\OpenWeatherMap;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use Dragonzap\OpenAI\ChatGPT\Assistant;
use Dragonzap\OpenAI\ChatGPT\ConversationIdentificationData;

class WeatherAssistant extends Assistant
{
    public function getAssistantId(): string
    {
        return config('dragonzap.assistants.weather.id');
    }

    /**
     * This function, 'get_weather', retrieves comprehensive weather information for a specified location. The function fetches details such as temperature, humidity, precipitation, and forecast. 
     * The 'location' parameter requires a string input in the format 'City, Country' or 'City, State' to ensure precise weather data retrieval.
     *
     * @function get_weather
     * @description Retrieves detailed weather information for a given location.
     * @param {object} parameters - Contains the parameters for the function.
     * @param {string} parameters.location - A string specifying the location (City, State/Country) for which weather information is needed.
     * Usage Example:
     * get_weather({ location: "London" });
     * 
     * You must add this function in your OpenAI Assistant https://platform.openai.com/assistants which will will allow ChatGPT to call the handleGetWeatherFunction method
     * {
     *   "name": "get_weather",
     *   "description": "Retrieves detailed weather information for a given location.",
     *   "parameters": {
     *     "type": "object",
     *     "properties": {
     *       "location": {
     *         "type": "string",
     *         "description": "The city and state/country, e.g., 'London, UK'."
     *       }
     *     },
     *     "required": [
     *       "location"
     *     ]
     *   }
     * }
     */

    protected function handleGetWeatherFunction(array $arguments): array
    {
        $response = ['state' => 'failed', 'message' => 'Unknown error'];
        $httpRequestFactory = new RequestFactory();
        $httpClient = GuzzleAdapter::createWithConfig([]);
        $owm = new OpenWeatherMap(config('services.openweathermap.key'), $httpClient, $httpRequestFactory);

        $location = $arguments['location'];
        // Below code sourced from: https://github.com/cmfcmf/OpenWeatherMap-PHP-API/blob/main/Examples/WeatherForecast.php

        try {
            $forecast = $owm->getWeather($location, 'metric');
            $response = [
                'state' => 'success',
                'forecast' => print_r($forecast, true)
            ];
        } catch (\Exception $ex) {
            $response = ['state' => 'failed', 'message' => 'Problem obtaining the weather for city ' . $location];
        }

        return $response;
    }

    /**
     * You must define this 'handle_weather' function within your ChatGPT assistant.
     * The function processes obtained weather data  and returns a corresponding weather icon URL. 
     * ChatGPT will invoke the function whenever weather data is retrieved from the get_weather function.
     *
     * JSON Configuration for handle_weather function:
     * {
     *   "name": "handle_weather",
     *   "description": "Processes weather data and returns a weather icon URL.",
     *   "parameters": {
     *     "type": "object",
     *     "properties": {
     *       "location": {
     *         "type": "string",
     *         "description": "The city location for which weather data is obtained."
     *       },
     *       "temperature": {
     *         "type": "number",
     *         "description": "Current temperature at the specified location."
     *       },
     *       "extra_notes": {
     *         "type": "string",
     *         "description": "Overview of the weather conditions in a simple sentence."
     *       },
     *       "category": {
     *         "type": "string",
     *         "enum": ["raining", "sunny", "clouds", "snowing"],
     *         "description": "The category of weather, used to determine the appropriate icon."
     *       }
     *     },
     *     "required": ["location", "temperature", "extra_notes", "category"]
     *   }
     * }
     * 
     * The method will simply return the icon_url to CHatGPT. Its not intended to be used by ChatGPT
     * it is instead extracted in resources/views/weather/index.blade.php after the API call. The 
     * client side javascript will then build a div to show the user the weather in the area they asked about.
     * 
     * Try calling the API in Postman to understand how this works
     */


    public function handleHandleWeatherFunction(array $arguments): array
    {
        $category = $arguments['category'];
        // In real life application you might have categories stored in the database
        // and a column in the table would represent the icon
        // Using  asset in this way as shown below is not recommended for production and only for testing purposes.
        $icon_url = asset('images/chat/icons/weather/' . $category . '.png');
        return [
            'state' => 'success',
            'chatgpt_info' => 'Do not display the icon url to the user or use it in anyway',
            'icon_url' => $icon_url
        ];
    }

    public function handleFunction(string $function, array $arguments): array|string
    {
        $response = [
            'success' => 'false',
            'message' => 'No functions implemented yet'
        ];
        switch ($function) {
            case 'get_weather':
                $response = $this->handleGetWeatherFunction($arguments);
                break;

            case 'handle_weather':
                $response = $this->handleHandleWeatherFunction($arguments);
                break;
        }

        return $response;
    }

    public function saveConversationIdentificationData(ConversationIdentificationData $conversation_id_data): void
    {
        // Not implemented.
    }
}