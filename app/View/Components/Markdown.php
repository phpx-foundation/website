<?php

namespace App\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use InvalidArgumentException;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Output\RenderedContentInterface;

class Markdown extends Component
{
	public function __construct(
		public ?string $file = null,
		public ?RenderedContentInterface $content = null,
		public bool $sidebar = false,
	) {
	}
	
	public function render()
	{
		return function($data) {
			$converter = app(ConverterInterface::class);
			
			$src = match (true) {
				null !== $this->file => file_get_contents($this->file),
				null !== $this->content => $this->content,
				$data['slot']->isNotEmpty() => $this->stripIndent($data['slot']->toHtml()),
				default => throw new InvalidArgumentException('No markdown provided!'),
			};
			
			$html = $src instanceof RenderedContentInterface ? $src : $converter->convert($src);
			
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
