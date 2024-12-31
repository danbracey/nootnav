<?php

namespace App;

use App\Services\UpdatePenguinData;
use Faker\Guesser\Name;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Laracord\Laracord;

class Bot extends Laracord
{
    public function beforeBoot(): void
    {
//        parent::beforeBoot(); // TODO: Call the API Service to get Penguin data
//        //TODO: Remove below once service can be called from here
//        $client = new Client();
//        $options = [
//            'multipart' => [
//                [
//                    'name' => 'data',
//                    'contents' => '{"id":"661f08a5d6fb81b89e0391cb"}'
//                ]
//            ]];
//        $request = new Request('POST', 'https://my.wildlifecomputers.com/data/map/data/');
//        $res = $client->sendAsync($request, $options)->wait();
//        $data = $res->getBody()->getContents();
//        $data = json_decode($data, true);
//
//        //Insert a new 'friendly name' for each penguin, so we're not dealing with Mig202x Gender number
//        // Check if 'deployment' exists and iterate through it
//
//        if (isset($data['deployments'])) {
//            foreach ($data['deployments'] as &$deployment) { // Use reference to modify the original array
//                // Check if 'title' contains 'Male' or 'Female' and set the gender
//                $gender = str_contains($deployment['title'], 'Male') ? 'male' : 'female';
//
//                // Assign a fake name based on the gender
//                $deployment['friendly_name'] = fake()->firstName($gender);
//            }
//        } else {
//            die("Unable to retrieve Penguin API Data, as such, this Bot cannot run");
//        }
//
//        $updatedData = json_encode($data);
//
//        // Save the updated data in storage
//        Storage::put('location_data.json', $updatedData);
    }

    /**
     * The HTTP routes.
     */
    public function routes(): void
    {
        Route::middleware('web')->group(function () {
            // Route::get('/', fn () => 'Hello world!');
        });

        Route::middleware('api')->group(function () {
            // Route::get('/commands', fn () => collect($this->registeredCommands)->map(fn ($command) => [
            //     'signature' => $command->getSignature(),
            //     'description' => $command->getDescription(),
            // ]));
        });
    }
}
