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

Route::get('/', function (FirstPrompt $prompt) {
    return response()->json($prompt->handle('Hello, who are you?'));
    return view('welcome');
});