import Alpine from 'alpinejs';
import Typed from 'typed.js';
import './flux-lite.js';
import '@oddbird/popover-polyfill';
import { apply, isPolyfilled, isSupported } from '@oddbird/popover-polyfill/fn';

if (! isSupported() && ! isPolyfilled()) {
	apply();
}

window.Alpine = Alpine;

Alpine.directive('typed', (el, { expression, modifiers }, { evaluateLater, effect, cleanup }) => {
	const getStrings = evaluateLater(expression);
	
	const modifierValue = (key, fallback) => {
		if (-1 === modifiers.indexOf(key)) {
			return fallback;
		}
		
		const value = modifiers[modifiers.indexOf(key) + 1];
		
		if (value && ! isNaN(fallback)) {
			return parseInt(value);
		}
		
		return value ? value : fallback;
	};
	
	effect(() => getStrings(strings => {
		const instance = new Typed(el, {
			strings,
			startDelay: modifierValue('delay', 750),
			typeSpeed: modifierValue('speed', 150),
			backSpeed: modifierValue('backspace', 100),
			showCursor: ! modifiers.includes('cursorless'),
			loop: modifiers.includes('loop'),
			cursorChar: '_',
		});
		cleanup(() => instance.destroy());
	}));
});

Alpine.data('onThisPage', () => ({
	headings: [],
	active_permalink: null,
	
	init() {
		this.headings = Array.from(document.querySelectorAll('.heading-permalink'))
			.map(node => ({
				title: node.parentNode.textContent.replace('#', ''),
				permalink: node.id,
				node: node.parentNode,
				level: parseInt(node.parentNode.tagName.replace(/\D/, '')),
				top: Infinity,
			}));
		
		this.onScroll();
	},
	
	onScroll() {
		this.headings.forEach(heading => {
			heading.top = heading.node.getBoundingClientRect().top;
		});
		
		const visible_headings = this.headings
			.filter(heading => heading.top < 200)
			.sort((a, b) => b.top - a.top);
		
		if (visible_headings.length === 0) {
			this.active_permalink = null;
			return;
		}
		
		this.active_permalink = visible_headings[0].permalink;
	},
}));

Alpine.start();
