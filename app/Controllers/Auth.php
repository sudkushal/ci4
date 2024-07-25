<?php

namespace App\Controllers;
use GuzzleHttp\Client;

class Auth extends BaseController
{
    public function index()
    {
        
        $client = new Client();
        $response = $client->request('GET', 'https://www.google.com');

        echo $response->getStatusCode(); // 200
        echo $response->getBody();
        //return view('landing_page'); // Load your new view file
    }
}
