<?php

namespace App\SlashCommands;

use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laracord\Commands\SlashCommand;
use PhpOption\Option;

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
            $path_string .= '&path=';

            foreach($deployment['locations'] as $location) {
                //Add each penguin's lat & long to the averages
                array_push($averages_lat, $location['latitude']);
                array_push($averages_long, $location['longitude']);
            }
        }

        $average_lat = array_sum($averages_lat) / count($averages_lat);
        $average_long = array_sum($averages_long) / count($averages_long);

        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('Tracking penguin: ' . $this->value('name', 'all'))
              ->content('Hello world! Avg. Lat: ' . $average_lat . ' Avg Long: ' . $average_long)
              ->imageUrl('https://maps.googleapis.com/maps/api/staticmap?center='
                  . $average_lat . ',' . $average_long
                  . '&size=500x400&key='
                  . config('app.map_api_key') //API KEY
                  . '&zoom=4'
              ) //Maps API
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
