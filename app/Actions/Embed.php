<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\asAction;
use Illuminate\Support\Str;
use \Probots\Pinecone\Client as Pinecone;
use OpenAI\Laravel\Facades\OpenAI;

class Embed
{
    use AsAction;

    public $commandSignature = 'embed';

    public function handle()
    {
        $pinecone_api_key = config('pinecone.api_key');
        $pinecone_environment = config('pinecone.environment');

        $pinecone = new Pinecone($pinecone_api_key, $pinecone_environment);

        $content = Str::of(File::get(storage_path('app/podcast.html')))
            ->after('<strong>')
            ->split('/<strong>/')
            ->map(fn(string $bit) => strip_tags($bit))
            ->toArray();

        $embeddings = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $content,
        ])->embeddings;

        $pinecone->index('chatbot')->vectors()->upsert(
            vectors: collect($embeddings)->map(fn($embedding, $index) => [
                'id' => (string) $index,
                'values' => $embedding->embedding,
                'metadata' => [
                    'text' => $content[$index]
                ]
            ])->toArray(),
            namespace: 'podcast'
        );
    }
}
