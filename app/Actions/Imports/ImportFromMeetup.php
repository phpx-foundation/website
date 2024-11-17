<?php

namespace App\Actions\Imports;

use App\Models\Group;
use App\Models\Meetup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class ImportFromMeetup
{
	use AsAction;

    public function getCommandSignature(): string
	{
		return 'meetup:import';
	}

    public function asCommand(Command $command)
	{
        // get all groups that have a meetup.com URL
        $groups = Group::whereNotNull('meetup_url')->get();

        foreach ($groups as $group) {

            $command->info('Crawling ' . $group->name);

            $events = collect([]);

            // if the meetup_url is an array, we need to crawl all of them
            if(is_array($group->meetup_url)) {
                foreach($group->meetup_url as $url) {
                    $events = $events->merge($this->crawl_meetupcom($url));
                }
            } else {
                $events = $this->crawl_meetupcom($group->meetup_url);
            }

            foreach ($events as $event) {

                $command->info('Importing ' . $event['title']);

                $meetup = Meetup::where('external_url', $event['external_url'])->first();

                if (!$meetup) {
                    $meetup = new Meetup();
                    $meetup->group_id = $group->id;
                    $meetup->external_url = $event['external_url'];
                }

                $meetup->starts_at = $event['time']->toDateTimeString();
                $meetup->ends_at = $event['time']->addHours(3)->toDateTimeString();
                $meetup->description = $this->prepareDescription($event['title'], $event['description'], $event['image'], $event['external_url']); 
                $meetup->location = $event['location'];
                $meetup->capacity = 100;
                $meetup->save();
            }
        }
    }

    private function prepareDescription($title, $description, $image, $external_url) {
        $output = "";

        // remove all classes from description
        $description = preg_replace('/class="[^"]*"/', '', $description);

        $output .= "<img src='$image' style='float:right; margin-left: 10px; margin-bottom: 10px; max-width: 200px; max-height: 200px;'>";
        $output .= "<h2>$title</h2>";
        $output .= "$description";

        // make sure that we link to meetup.com so that they are not unhappy with us.
        $output .= "<p>This event is imported from meetup.com - <a href='$external_url' target='_blank'>Go to Meetup.com Event</a></p>";

        return $output;
    }

    private function crawl_meetupcom($url) {

        $meetup_page = Cache::remember($url.'1', 60 * 60 * 24, function () use ($url) {
            return Http::get($url)->body();
        });

        $dom = new \DOMDocument();
        @$dom->loadHTML($meetup_page);
        $xpath = new \DOMXPath($dom);

        // meetup.com has the events in teasers that have an ID that starts with 'event-card'
        $events = $xpath->query("//a[starts-with(@id,'event-card')]");

        $output = collect([]);
        foreach ($events as $event) {

            // get the external URL
            $external_url = $event->getAttribute('href');

            // get the starting_at time
            $time_raw = $xpath->query('//time', $event)->item(0)->textContent;
            $time = Carbon::parse($time_raw);

            // get the title
            $title = $xpath->query('//time', $event)->item(0)->nextSibling;

            // get the location
            $location = $title->nextSibling;

            // get the description
            $description = $xpath->query("//div[starts-with(@class, 'utils_cardDescription')]", $event)->item(0);
            $description_html = $dom->saveHTML($description);

            // get the teaser image
            $image = $xpath->query('//img', $event)->item(0)->getAttribute('src');

            // get external URL without ? paramers
            $external_url = explode('?', $external_url)[0];

            $output->push([
                'external_url' => $external_url,
                'time' => $time,
                'title' => $title->textContent,
                'description' => $description_html,
                'location' => $location->textContent,
                'image' => $image,
            ]);
        }

        return $output;
    }
}
