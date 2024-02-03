<?php

namespace App\Actions;

// Import necessary classes and facades
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

// Define a class named FirstPrompt
class FirstPrompt
{
    // Use the AsAction trait to enable this class to be used both as an action and a command
    use AsAction;
    // Define a command signature for the Laravel console
    public $commandSignature = 'inspire {prompt : The user propmt}';
    // Define a handle method that takes a string prompt as input
    public function handle(array $messages)
    {
        // Create and return a chat session with the OpenAI API
        return OpenAI::chat()->create([
            // Set the model to 'gpt-3.5-turbo'
            'model' => 'gpt-3.5-turbo',
            // Define the initial message from the user
            'messages' => $messages,
        ]);
    }
    // Define a method to handle this action as a console command
    public function asCommand(Command $command)
    {
        // Output the result of the handle function to the console
        $command->comment($this->handle($command->argument('prompt')));
    }
}
