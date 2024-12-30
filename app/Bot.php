<?php

namespace App;

use Illuminate\Support\Facades\Route;
use Laracord\Laracord;

class Bot extends Laracord
{
    public function beforeBoot(): void
    {
        parent::beforeBoot(); // TODO: Call the API Service to get Penguin data
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
