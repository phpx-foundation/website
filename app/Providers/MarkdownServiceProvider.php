<?php

namespace App\Providers;

use App\Enums\RootDomains;
use App\Http\Controllers\World\MarkdownController;
use App\Support\Markdown\TaggedBlockQuoteRenderer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use Symfony\Component\Finder\Finder;

class MarkdownServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton(ConverterInterface::class, function(Application $app) {
			$converter = new GithubFlavoredMarkdownConverter($this->markdownConfig());
			
			$converter->getEnvironment()
				->addExtension(new DefaultAttributesExtension())
				->addExtension(new HeadingPermalinkExtension())
				->addExtension(new FrontMatterExtension())
				->addRenderer(BlockQuote::class, new TaggedBlockQuoteRenderer());
			
			return $converter;
		});
		
		if (! $this->app->routesAreCached()) {
			$this->registerMarkdownRoutes();
		}
	}
	
	public function boot(): void
	{
		
	}
	
	protected function registerMarkdownRoutes(): void
	{
		$this->app->booted(function() {
			$files = Finder::create()
				->files()
				->name('*.md')
				->in(resource_path('markdown/pages'));
			
			foreach ($files as $file) {
				$subdirectory = trim($file->getRelativePath(), '/');
				$name = $file->getBasename('.md');
				
				foreach (RootDomains::cases() as $case) {
					Route::domain($case->value)
						->group(fn() => Route::get("{$subdirectory}/{$name}", MarkdownController::class)
							->defaults('subdirectory', $subdirectory)
							->defaults('name', $name)
						);
				}
			}
		});
	}
	
	protected function markdownConfig(): array
	{
		return [
			'heading_permalink' => [
				'html_class' => 'heading-permalink scroll-mt-12 mr-1 relative no-underline opacity-50 hover:opacity-90',
				'symbol' => '#',
			],
			'default_attributes' => [
				Heading::class => [
					// 'class' => static fn(Heading $node) => match ($node->getLevel()) {
					// 	1 => 'text-5xl font-mono font-bold text-white text-center sm:text-left lg:text-6xl',
					// 	default => '',
					// },
				],
				Paragraph::class => [
					// 'class' => 'text-lg lg:text-xl leading-normal mb-4',
				],
				BlockQuote::class => [
					// 'class' => 'ml-6 pl-4 border-l-4 border-gray-100 italic text-gray-600 text-lg lg:text-xl leading-normal mb-4',
				],
				IndentedCode::class => [
					// 'class' => 'block w-full overflow-x-auto leading-normal mb-4',
				],
				FencedCode::class => [
					// 'class' => 'block w-full leading-normal mb-4 overflow-x-auto',
				],
				Code::class => [
					// 'class' => 'inline-block bg-gray-50 border border-gray-100 rounded font-mono px-2 py-0 m-0 text-purple-600',
				],
				Link::class => [
					// 'class' => 'text-blue-800 underline hover:text-blue-500',
				],
				Image::class => [
					// 'class' => 'rounded-lg my-8',
				],
			],
		];
	}
}
