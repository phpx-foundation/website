<?php

namespace App\Actions;

use App\Actions\Concerns\FetchesModelsForCommands;
use App\Models\Meetup;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\Colors\Rgb\Color;
use Lorisleiva\Actions\Concerns\AsAction;
use SimonHamp\TheOg\BorderPosition;
use SimonHamp\TheOg\Image;
use SimonHamp\TheOg\Interfaces\Font;
use SimonHamp\TheOg\Layout\Layouts\GitHubBasic;
use SimonHamp\TheOg\Theme;

class GenerateOpenGraphImage
{
    use AsAction;
    use FetchesModelsForCommands;

    public function handle(Meetup $meetup): string
    {
        $filename = storage_path("app/public/og/meetups/{$meetup->getKey()}.png");

        (new Filesystem)->ensureDirectoryExists(dirname($filename));

        $font = new class implements Font
        {
            public function path(): string
            {
                return resource_path('og/fonts/FiraSans-Regular.ttf');
            }
        };

        $theme = new Theme\Theme(
            accentColor: '#ffffff',
            baseFont: $font,
            baseColor: '#ffffff',
            backgroundColor: '#000000',
            background: new Theme\Background(resource_path('og/images/dots.png')),
            borderColor: '#000000',
            callToActionBackgroundColor: '#000000',
            callToActionColor: '#ffffff',
            callToActionFont: $font,
            descriptionColor: '#808080',
            descriptionFont: $font,
            titleColor: '#ffffff',
            titleFont: $font,
            urlColor: '#808080',
            urlFont: $font,
        );

        (new Image)
            ->theme($theme)
            ->layout(new GitHubBasic)
            ->border(BorderPosition::All, new Color(0, 0, 0, 0), 100)
            ->url($meetup->group->domain)
            ->title("Meetup @ {$meetup->location}")
            ->description($meetup->range())
            ->watermark(new Theme\Picture(resource_path('og/images/phpx.png')))
            ->save($filename);

        return $filename;
    }

    public function getCommandSignature(): string
    {
        return 'meetup:generate-og {meetup?}';
    }

    public function asCommand(Command $command): int
    {
        $meetup = $this->getMeetupFromCommand($command);
        $filename = $this->handle($meetup);

        $command->line("Wrote og image to <info>{$filename}</info>");

        return 0;
    }
}
