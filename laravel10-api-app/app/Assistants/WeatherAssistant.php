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
        
        // Example 1: Get forecast for the next 5 days for Berlin.
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
        return [
            'state' => 'success',
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