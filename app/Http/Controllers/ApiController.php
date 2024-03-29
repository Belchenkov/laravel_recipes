<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

class ApiController extends Controller
{
    public function github($username)
    {
        $client = new GuzzleClient();
        $response = $client->get('https://api.github.com/users/' . $username);
        $body = json_decode($response->getBody());
        
        return $body;
    }

    public function getWeather()
    {
        return view('weather');
    }

    public function postWeather(Request $request)
    {
        $this->validate($request, ['location' => 'required|min:5']);

        // google api to get coords
        $googleClient = new GuzzleClient();
        $response = $googleClient->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $request->location
            ]
        ]);

        $googleBody = json_decode($response->getBody());
        $coords = $googleBody->results[0]->geometry->location;

        // use the coords to get weather from darksky
        $dsClient = new GuzzleClient();
        $dsUrl = 'https://api.darksky.net/forecast/'.env('DARKSKY_API')."/$coords->lat,$coords->lng";
        $dsResponse = $dsClient->get($dsUrl);
        $weatherBody = json_decode($dsResponse->getBody());

        return view('weather-ready')->with('weather', $weatherBody)->with('address', $googleBody->results[0]);

    }
}
