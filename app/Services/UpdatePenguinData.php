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
        $data = $res->getBody()->getContents();

        //Insert a new 'friendly name' for each penguin, so we're not dealing with Mig202x Gender number
        $data = json_decode($data, true);
        // Check if 'deployment' exists and iterate through it

        if (isset($data['deployments'])) {
            foreach ($data['deployments'] as &$deployment) { // Use reference to modify the original array
                // Check if 'title' contains 'Male' or 'Female' and set the gender
                $gender = str_contains($deployment['title'], 'Male') ? 'male' : 'female';

                // Assign a fake name based on the gender
                $deployment['friendly_name'] = fake()->firstName($gender);
            }
        } else {
            die("Unable to retrieve Penguin API Data, as such, this Bot cannot run");
        }

        $updatedData = json_encode($data);

        // Save the updated data in storage
        Storage::put('location_data.json', $updatedData);
    }
}
