<?php

namespace App\Http\Controllers\World;

use Illuminate\Support\Arr;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Output\RenderedContentInterface;

class MarkdownController
{
	public function __invoke(string $subdirectory, string $name)
	{
		$content = $this->loadMarkdown($subdirectory, $name);
		$metadata = $this->getMetadata($name, $content);
		
		return view('world.markdown', [
			'title' => $metadata['title'],
			'sidebar' => $metadata['sidebar'],
			'content' => $content,
		]);
	}
	
	protected function loadMarkdown(string $subdirectory, string $name): RenderedContentInterface
	{
		if (! file_exists($path = resource_path("markdown/pages/$subdirectory/$name.md"))) {
			abort(404);
		}
		
		return app(ConverterInterface::class)
			->convert(file_get_contents($path));
	}
	
	protected function getMetadata(string $name, RenderedContentInterface $content): array
	{
		$title = str($name)
			->beforeLast('.md')
			->replace('-', ' ')
			->apa()
			->toString();
		
		$sidebar = true;
		
		if ($content instanceof RenderedContentWithFrontMatter) {
			$meta = $content->getFrontMatter();
			
			$title = Arr::get($meta, 'title', $title);
			$sidebar = (bool) Arr::get($meta, 'sidebar', $sidebar);
		}
		
		return [
			'title' => $title,
			'sidebar' => $sidebar,
		];
	}
}
