<?php

// Import necessary classes and facades for the functionality
// IMPORT necessary classes for Artisan commands, OpenAI API interaction

use App\Actions\FirstPrompt;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use OpenAI\Laravel\Facades\OpenAI;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Define a new console command named 'inspire'
Artisan::command('inspire', function () {
    $this->comment(
        // Create a chat session with OpenAI using specific parameters
        OpenAI::chat()->create([
            // Set the model to 'gpt-3.5-turbo'
            'model' => 'gpt-3.5-turbo',
            // Define the initial message from the user
            'messages' => [
                [
                'role' => 'user', // Indicate the message is from the user
                'content' => 'Hello, Who are you?', // The actual message content
                ]
            ],
        // From the response, select the first choice and get its message content
        ])->choices[0]->message->content
    );
// Set the purpose of the command to display an inspiring quote
})->purpose('Display an inspiring quote');
