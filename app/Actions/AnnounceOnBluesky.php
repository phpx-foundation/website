<?php

namespace App\Actions;

use App\Actions\Concerns\FetchesModelsForCommands;
use App\Models\Meetup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Revolution\Bluesky\Embed\External;
use Revolution\Bluesky\RichText\TextBuilder;
use Throwable;
use UnexpectedValueException;

class AnnounceOnBluesky
{
    use AsAction;
    use FetchesModelsForCommands;

    public function handle(Meetup $meetup): string
    {
        $bsky = $meetup->group->bsky();

        $post = TextBuilder::make('📆 ')
            ->link(text: "Meetup @ {$meetup->location}", uri: $meetup->rsvp_url)
            ->newLine()
            ->newLine()
            ->text($meetup->range())
            ->newLine()
            ->newLine()
            ->tag(text: '#Meetup', tag: 'Meetup')
            ->text(' ')
            ->tag(text: '#PHP', tag: 'PHP')
            ->text(' ')
            ->tag(text: '#Laravel', tag: 'Laravel')
            ->toPost();

        if ($meetup->open_graph_image_file) {
            $thumbnail = $bsky->uploadBlob(Storage::get($meetup->open_graph_image_file));

            dump($thumbnail->json(), $meetup->open_graph_image_file);

            $post->embed(External::create(
                title: $meetup->group->name,
                description: "Meetup @ {$meetup->location} on {$meetup->range()}",
                uri: $meetup->rsvp_url,
                thumb: $thumbnail->json('blob'),
            ));
        }

        $post->createdAt(now()->toRfc3339String());

        $response = $bsky->post($post);
        $uri = str($response->json('uri'));

        try {
            [$did, $collection, $rkey] = $uri->after('at://')->explode('/');
        } catch (Throwable) {
            throw new UnexpectedValueException("Did not get a valid post: {$response->body()}");
        }

        if ($collection !== 'app.bsky.feed.post') {
            throw new UnexpectedValueException("Did not get a post: {$response->body()}");
        }

        return "https://bsky.app/profile/{$did}/post/{$rkey}";
    }

    public function getCommandSignature(): string
    {
        return 'bsky:announce {meetup?}';
    }

    public function asCommand(Command $command): int
    {
        $meetup = $this->getMeetupFromCommand($command, upcoming: true);

        $url = $this->handle($meetup);

        $command->line("Posted at <info>{$url}</info>");

        return 0;
    }
}
