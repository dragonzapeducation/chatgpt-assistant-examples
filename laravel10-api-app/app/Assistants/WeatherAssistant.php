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
        return 'asst_cWrXNjOWrfhacZSKzjrOvwKq';
    }

    protected function handleGetWeatherFunction(array $arguments) : array
    {
        $response = ['state' => 'failed', 'message' => 'Unknown error'];
        $httpRequestFactory = new RequestFactory();
        $httpClient = GuzzleAdapter::createWithConfig([]);
        $owm = new OpenWeatherMap(config('services.openweathermap.key'), $httpClient, $httpRequestFactory);

        $location = $arguments['location'];
        // Below code sourced from: https://github.com/cmfcmf/OpenWeatherMap-PHP-API/blob/main/Examples/WeatherForecast.php
        
        try
        {
            $forecast = $owm->getWeather($location, 'metric');
            $response = [
                'state' => 'success',
                'forecast' => print_r($forecast, true)
            ];
        } catch(\Exception $ex) {
            $response = ['state' => 'failed', 'message' => 'Problem obtaining the weather for city ' . $location];
        }

        return $response;
    }

    public function handleHandleWeatherFunction(array $arguments) : array
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
        $response =  [
            'success' => 'false',
            'message' => 'No functions implemented yet'
        ];
        switch($function)
        {
            case 'get_weather':
                $response = $this->handleGetWeatherFunction($arguments);
            break;

            case 'handle_weather':
                $response = $this->handleHandleWeatherFunction($arguments);
            break;
        }

        return $response;
    }

    public function saveConversationIdentificationData(ConversationIdentificationData $conversation_id_data) : void
    {
        // Not implemented.
    }
}