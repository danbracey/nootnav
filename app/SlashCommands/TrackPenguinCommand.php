<?php

namespace App\SlashCommands;

use Discord\Parts\Interactions\Interaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laracord\Commands\SlashCommand;
use PhpOption\Option;
use Discord\Parts\Embed\Embed;

class TrackPenguinCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'track';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Track a penguin! Do /penguins to list available penguins';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [
        [
            'name' => 'name',
            'description' => 'Which penguin would you like to track?',
            'type' => \Discord\Parts\Interactions\Command\Option::STRING,
            'required' => false
        ]
    ];

    /**
     * Set the autocomplete choices.
     */
    public function autocomplete(): array
    {
        $data = json_decode(Storage::get('location_data.json'), true);

        $penguins = [];
        foreach($data['deployments'] as $penguin) {
            array_push($penguins, $penguin['friendly_name']);
        }

        return [
            'name' => $penguins,
        ];
    }

    /**
     * The permissions required to use the command.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Indicates whether the command requires admin permissions.
     *
     * @var bool
     */
    protected $admin = false;

    /**
     * Indicates whether the command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Handle the slash command.
     *
     * @param  \Discord\Parts\Interactions\Interaction  $interaction
     * @return mixed
     */
    public function handle($interaction)
    {
        //Work out the average position for the map centre
        $averages_lat = [];
        $averages_long = [];
        $path_string = '';  //Create a path for each penguin

        $data = json_decode(Storage::get('location_data.json'), true);
        foreach($data['deployments'] as $deployment) {
            //Start a new path for each penguin
            $path_string .= '&path=color:0x000000FF|weight:1';

            foreach($deployment['locations'] as $key => $location) {
                if ( $key==0 || ($key+1)%18 == 0 ) //Attempt to reduce the number of path points, to every 5th point.
                {
                    //Add each penguin's lat & long to the averages
                    array_push($averages_lat, $location['latitude']);
                    array_push($averages_long, $location['longitude']);

                    //Add each penguin's location to the path
                    $path_string .= '|'. $location['latitude'] . ',' . $location['longitude'];
                }
            }
        }

        $average_lat = array_sum($averages_lat) / count($averages_lat);
        $average_long = array_sum($averages_long) / count($averages_long);

        $client = new Client();

        try {
            // Send a GET request to the Google Static Maps API
            $response = $client->get('https://maps.googleapis.com/maps/api/staticmap?center='
                . $average_lat . ',' . $average_long
                . '&size=500x400&key='
                . config('app.map_api_key') //API KEY
                . '&zoom=5'
                . $path_string);

            // Get the image body from the response (raw image data)
            $image_data = $response->getBody();

            // Step 4: Save the image as a PNG file
            Storage::put('track.png', $image_data);
            //file_put_contents("storage/track.png", $image_data);
            echo "Image saved successfully as google_map_image.png";

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle errors (e.g., invalid API key, network issues)
            echo "Error fetching the image: " . $e->getMessage();
            $interaction->respondWithMessage(
                $this
                    ->message('Unable to fetch tracking Map')
                    ->build()
            );
        }

        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('Tracking penguin: ' . $this->value('name', 'all'))
              ->content('Tracking ' . count($data['deployments']) . ' penguins:')
              ->filePath('./storage/track.png') //Maps API
              ->build()
        );
    }

    /**
     * The command interaction routes.
     */
    public function interactions(): array
    {
        return [];
    }
}
