<?php

namespace App\Actions;

use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use OpenAI\Laravel\Facades\OpenAI;

class FirstPrompt
{
    use AsAction;

    public $commandSignature = 'inspire {prompt : The user propmt}';

    public function handle(string $prompt)
    {
        // Create a chat session with OpenAI using specific parameters
        return OpenAI::chat()->create([
            // Set the model to 'gpt-3.5-turbo'
            'model' => 'gpt-3.5-turbo',
            // Define the initial message from the user
            'messages' => [
                [
                    'role' => 'user', // Indicate the message is from the user
                    'content' => $prompt, // The actual message content
                ]
            ],
            // From the response, select the first choice and get its message content
        ]);
    }

    public function asCommand(Command $command)
    {
        $command->comment($this->handle($command->argument('prompt')));
    }
}
