<?php

use \Probots\Pinecone\Client as Pinecone;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Actions\StreamingPrompt;
use App\Actions\FirstPrompt;
use App\Actions\EmbedWeb;
use App\SearchClient;
use App\Actions\GetWebpageContent;
use App\Actions\CondenseText;
use App\Actions\AssessWebAccessRequirement;
use Illuminate\Support\Facades\Process;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// webpage
// text inout
// form
// show response
// growing list

// a route conversation page
// a route form submission

// conversation model
// message model

// create conversation
// save prompts and responses
Route::get('/conversation/{id}', function ($id) {
    $conversation = $id == 'new' ? null : Conversation::find($id);
    return view('conversation', [
        'conversation' => $conversation
    ]);
})->name('conversation');

Route::post('chat/{id}', function (Request $request, FirstPrompt $prompt, $id) {
    if ($id == 'new') {
        $conversation = Conversation::create();
    } else {
        $conversation = Conversation::find($id);
    }

    $conversation->messages()->create([
        'content' => $request->input('prompt'),
        'role' => 'user',
    ]);

    $messages = $conversation->messages->map(function (Message $message) {
        return [
            'content' => $message->content,
            'role' => 'user',
        ];
    })->toArray();

    $systemMessage = [
        'role' => 'system',
        'content' => 'The user\'s name is Danny',
    ];

    $result = $prompt->handle(array_merge([$systemMessage], $messages));

    $conversation->messages()->create([
        'content' => $result->choices[0]->message->content,
        'role' => 'assistant',
    ]);

    return redirect()->route('conversation', ['id' => $conversation->id]);
})->name('chat');


Route::get('/', function (FirstPrompt $prompt) {
    $pinecone_api_key = config('pinecone.api_key');
    $pinecone_index = config('pinecone.index');
    $pinecone_environment = config('pinecone.environment');

    $pinecone = new Pinecone($pinecone_api_key, $pinecone_environment);

    $values1 = [
        'My name is Omniman',
        // 'I live on viltrumite',
        'Badgers just don\'t care',
    ];

    $values2 = [
        'My name is Danny',
        'I live in San Diego',
        // 'Badgers just don\'t care about me',
    ];

    $embeddings = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => $values1,
    ])->embeddings;

    $result = $pinecone->index($pinecone_index)->vectors()->upsert(
        collect($embeddings)->map(fn($embedding, $idx) => [
            'id' => (string) $idx,
            'values' => $embedding->embedding,
            'metadata' => [
                'text' => $values1[$idx]
            ]
        ])->toArray()
    , namespace: 'Omniman');

    $embeddings = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => $values2,
    ])->embeddings;

    $result = $pinecone->index($pinecone_index)->vectors()->upsert(
        collect($embeddings)->map(fn($embedding, $idx) => [
            'id' => (string) $idx,
            'values' => $embedding->embedding,
            'metadata' => [
                'text' => $values2[$idx]
            ]
        ])->toArray()
    , namespace: 'Danny');

    $question = OpenAI::embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => [
            'Tell me something about me.',
        ]
    ]);

    $result = $pinecone->index($pinecone_index)->vectors()->query(vector: $question->embeddings[0]->embedding, namespace: 'Danny', topK: 4)->json();
    dd($result);
});