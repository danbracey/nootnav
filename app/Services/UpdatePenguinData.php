<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;
use Laracord\Services\Service;

class UpdatePenguinData extends Service
{
    /**
     * The service interval.
     *
     * Fetch new penguin locations every 2 hours. Give them a chance to rest those flippers
     */
    protected int $interval = 7200;

    /**
     * Handle the service.
     */
    public function handle(): mixed
    {
        $client = new Client();
        $options = [
            'multipart' => [
                [
                    'name' => 'data',
                    'contents' => '{"id":"661f08a5d6fb81b89e0391cb"}'
                ]
            ]];
        $request = new Request('POST', 'https://my.wildlifecomputers.com/data/map/data/');
        $res = $client->sendAsync($request, $options)->wait();
        //$data = json_decode($res->getBody()->getContents(), true);

        Storage::put('location_data.json', $res->getBody()->getContents());
    }
}
