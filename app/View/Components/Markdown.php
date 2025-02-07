<?php

namespace App\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use InvalidArgumentException;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Renderer\Block\BlockQuoteRenderer;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class Markdown extends Component
{
	public function __construct(
		public ?string $file = null,
		public bool $sidebar = false,
	) {
	}
	
	public function render()
	{
		return function($data) {
			$converter = new GithubFlavoredMarkdownConverter($this->config());
			
			$environment = $converter->getEnvironment()
				->addExtension(new DefaultAttributesExtension())
				->addExtension(new HeadingPermalinkExtension());
			
			$this->addSpecialBlockQuotes($environment);
			
			// ->addExtension(new TorchlightExtension());
			
			$src = match (true) {
				null !== $this->file => file_get_contents($this->file),
				$data['slot']->isNotEmpty() => $this->stripIndent($data['slot']->toHtml()),
				default => throw new InvalidArgumentException('No markdown provided!'),
			};
			
			$html = $converter->convert($src);
			
			$html = "<div {$data['attributes']->merge(['class' => 'prose prose-invert'])}>{$html}</div>";
			
			if ($this->sidebar) {
				$html = <<<HTML
				<div class="w-full flex items-start justify-start">
					<article class="flex-1 max-w-full flex-shrink mb-64 overflow-x-auto">
						{$html}
					</article>
					{$this->sidebar()}
				</div>
				HTML;
				
			}
			
			return new HtmlString($html);
		};
	}
	
	public function resolveView()
	{
		return $this->render();
	}
	
	protected function sidebar(): string
	{
		return <<<'HTML'
			<aside
				x-data="onThisPage"
				x-on:scroll.window.throttle.50ms="onScroll()"
				x-show="headings.length > 1"
				class="hidden top-4 w-64 flex-shrink-0 min-h-0 sticky overflow-y-auto py-8 pl-6 lg:block"
			>
				<h4 class="mb-2 block text-sm font-bold uppercase opacity-70 text-white">
					On this page
				</h4>
				
				<ul>
					<template x-for="heading in headings">
						<li
							class="text-sm"
							:class="{
								'mt-3': heading.level === 2 || heading.level === 1,
								'pl-2': heading.level === 3,
								'pl-4': heading.level === 4,
								'pl-6': heading.level === 5,
								'pl-8': heading.level === 6
							}"
						>
							<a
								:href="`#${heading.permalink}`"
								class="text-white hover:opacity-90"
								:class="{ 
									'font-medium opacity-70': active_permalink === heading.permalink, 
									'opacity-50': active_permalink !== heading.permalink,
								}"
								x-text="heading.title"
							></a>
						</li>
					</template>
				</ul>
			</aside>
		HTML;
	}
	
	protected function addSpecialBlockQuotes(Environment $environment)
	{
		$environment->addRenderer(BlockQuote::class,
			new class implements NodeRendererInterface {
				public function render(Node $node, ChildNodeRendererInterface $childRenderer)
				{
					$renderer = new BlockQuoteRenderer();
					[$classes, $heading_classes, $heading, $svg] = $this->parseNode($node);
					
					if ($svg) {
						return new HtmlElement(
							tagName: 'div',
							attributes: ['class' => $classes],
							contents: [
								new HtmlElement('div', ['class' => $heading_classes], [$svg, $heading]),
								$childRenderer->renderNodes($node->firstChild()->children()),
							],
						);
					}
					
					return $renderer->render($node, $childRenderer);
				}
				
				protected function parseNode(Node $node): array
				{
					$paragraph = $node->firstChild();
					$child = $paragraph?->firstChild();
					
					if ($child instanceof Text && preg_match('#^\[!(note|tip|important|warning|caution)]$#i', $child->getLiteral(), $matches)) {
						$child->detach();
						
						return match ($matches[1]) {
							'note' => [
								'border-l-4 border-blue-300 pl-4 py-2',
								'text-blue-300 flex items-center font-semibold',
								'Note',
								'<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>',
							],
							'tip' => [
								'border-l-4 border-green-300 pl-4 py-2',
								'text-green-300 flex items-center font-semibold',
								'Tip',
								'<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>',
							],
							'important' => [
								'border-l-4 border-purple-300 pl-4 py-2',
								'text-purple-300 flex items-center font-semibold',
								'Important',
								'<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>',
							],
							'warning' => [
								'border-l-4 border-yellow-600 pl-4 py-2',
								'text-yellow-600 flex items-center font-semibold',
								'Warning',
								'<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>',
							],
							'caution' => [
								'border-l-4 border-red-500 pl-4 py-2',
								'text-red-500 flex items-center font-semibold',
								'Caution',
								'<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 animate-pulse mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>',
							],
						};
					}
					
					return [null, null];
				}
			});
	}
	
	protected function config(): array
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
	
	protected function stripIndent(string $markdown): string
	{
		// Because Laravel trims the string, we have to ignore the first line
		$lines = explode("\n", $markdown);
		$first_line = array_shift($lines);
		$other_lines = implode("\n", $lines);
		
		preg_match_all('/^[ \t]*(?=\S)/m', $other_lines, $matches);
		$indent = array_reduce($matches[0], fn($indent, $match) => min($indent, strlen($match)), PHP_INT_MAX);
		
		if (PHP_INT_MAX === $indent) {
			return $markdown;
		}
		
		return $first_line."\n".preg_replace('/^[\t ]{'.$indent.'}/m', '', $other_lines);
	}
}
