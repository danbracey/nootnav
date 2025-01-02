<?php

namespace App\SlashCommands;

use Discord\Parts\Interactions\Interaction;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laracord\Commands\SlashCommand;

class ListPenguinsCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'penguins';

    protected $guild = '1323298173489250304';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Lists the names of all the penguins';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [];

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
        $data = json_decode(Storage::get('location_data.json'), true);

        $penguins = [];
        $content = '';
        foreach($data['deployments'] as $penguin) {
            $gender = null;
            str_contains($penguin['title'], 'Male') ? $gender = ":male_sign:" : $gender = ":female_sign:";
            $content .= '* ' . $penguin['friendly_name'] . ' ' . $gender . "\n";
            array_push($penguins, $penguin);
        }

        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('Available penguins:')
              ->content($content)
              ->build()
        );
    }

    /**
     * The command interaction routes.
     */
    public function interactions(): array
    {
        return [
            //
        ];
    }
}
