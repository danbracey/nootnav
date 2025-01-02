<?php

namespace App\SlashCommands;

use Discord\Parts\Interactions\Interaction;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;
use Laracord\Commands\SlashCommand;

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
//        [
//            'name' => 'name',
//            'description' => 'Which penguin would you like to track?',
//            'type' => \Discord\Parts\Interactions\Command\Option::STRING,
//            'required' => false
//        ]
    ];

    /**
     * Set the autocomplete choices.
     */
    public function autocomplete(): array
    {
//        $data = json_decode(Storage::get('location_data.json'), true);
//
//        $penguins = [];
//        foreach($data['deployments'] as $penguin) {
//            array_push($penguins, $penguin['friendly_name']);
//        }
//
//        return [
//            'name' => $penguins,
//        ];
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
     * @param Interaction $interaction
     * @return mixed
     * @throws Exception|GuzzleException
     */
    public function handle($interaction)
    {
        //Work out the average position for the map centre
        $averages_lat = [];
        $averages_long = [];
        $path_string = '';  //Create a path for each penguin
        $path_colors = [
            '0x55ABEE' => ':blue_square:',
            '0xC0684F' => ':brown_square:',
            '0x77B058' => ':green_square:',
            '0xF48F0B' => ':orange_square:',
            '0xDC2D44' => ':red_square:'
        ];

        $path_keys = array_keys($path_colors);
        $path_values = array_values($path_colors);
        $items_per_page = 5;  // Items per page
        $page = 1;            // Current page (for example, 1 for first, 2 for second, etc.)

        // Calculate the starting index and ending index for the data
        $start_index = ($page - 1) * $items_per_page;
        $end_index = $start_index + $items_per_page;
        $content = '';

        $data = json_decode(Storage::get('location_data.json'), true);

        for ($i = $start_index; $i < $end_index; $i++) {
            //Start a new path for each penguin
            $path_string .= '&path=color:' . $path_keys[$i] . '|weight:2';
            $penguin = $data['deployments'][$i];

            $gender = null;
            str_contains($penguin['title'], 'Male') ? $gender = ":male_sign:" : $gender = ":female_sign:";
            $content .= $path_values[$i] . ' ' . $penguin['friendly_name'] . ' ' . $gender . "\n";

            foreach($penguin['locations'] as $key => $location) {
                if ( $key==0 || ($key+1)%3 == 0 ) //Due to chunking the results by every 5 penguins, we can increase the accuracy of data to one in every 3 path points.
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
            if(! Storage::exists('track.png') || time() - Storage::lastModified('track.png') > 7200) {
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

                echo "Generated a new tracking map";
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle errors (e.g., invalid API key, network issues)
            echo "Error fetching the image: " . $e->getMessage();
            $interaction->respondWithMessage(
                $this
                    ->message('Unable to fetch tracking Map')
                    ->build()
            );
        }

        $null = null;
        $interaction->respondWithMessage(
            $this
              ->message()
              ->body('<a:nootnoot:1323756516855124048> Tracking ' . count($data['deployments']) . ' penguins total (displaying ' . $start_index + 1 . ' to ' . $end_index . ')')
              ->title('<a:nootnoot:1323756516855124048> Map Key: ')
              ->content($content)
              //->footerText("Use /track [name] to track a singular penguin!")
              ->filePath('./storage/track.png') //Maps API
              ->button('<', disabled: $page < 2, route: 'back')
              ->button('>', disabled: count($data['deployments']) % $items_per_page !== 0, route: 'forward')
              ->build()
        );
    }

    /**
     * The command interaction routes.
     */
    public function interactions(): array
    {
        return [
            'back' => fn (Interaction $interaction) =>
            $this
                ->message("You selected {$interaction->data->values[0]}.")
                ->reply($interaction, ephemeral: true),

            'forward' => fn (Interaction $interaction) =>
            $this
                ->message("You selected {$interaction->data->values[0]}.")
                ->reply($interaction, ephemeral: true),
        ];
    }
}
