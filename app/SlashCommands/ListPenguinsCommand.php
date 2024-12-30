<?php

namespace App\SlashCommands;

use Discord\Parts\Interactions\Interaction;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Laracord\Commands\SlashCommand;

class ListPenguinsCommand extends SlashCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'penguins';

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
        $data = json_decode($res->getBody()->getContents(), true);

        $penguins = [];
        foreach($data['deployments'] as $penguin) {
            array_push($penguins, $penguin['title']);
        }


        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('Available penguins:')
              ->content(implode(',', $penguins))
              ->button('👋', route: 'wave')
              ->build()
        );
    }

    /**
     * The command interaction routes.
     */
    public function interactions(): array
    {
        return [
            'wave' => fn (Interaction $interaction) => $this->message('👋')->reply($interaction),
        ];
    }
}
