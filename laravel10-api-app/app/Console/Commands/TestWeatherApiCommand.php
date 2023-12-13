<?php

namespace App\Console\Commands;

use Cmfcmf\OpenWeatherMap;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;

use Illuminate\Console\Command;

class TestWeatherApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather-api:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the OpenWeatherMap API ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $httpRequestFactory = new RequestFactory();
        $httpClient = GuzzleAdapter::createWithConfig([]);
        $owm = new OpenWeatherMap(config('services.openweathermap.key'), $httpClient, $httpRequestFactory);

        // Below code sourced from: https://github.com/cmfcmf/OpenWeatherMap-PHP-API/blob/main/Examples/WeatherForecast.php
        
        // Example 1: Get forecast for the next 5 days for Berlin.
        $forecast = $owm->getWeather('Cardiff', 'metric');
        print_r($forecast);
    }
}
