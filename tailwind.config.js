/** @type {import('tailwindcss').Config} */
export default {
	content: [
		"./app/View/**/*.php",
		"./resources/**/*.php",
		"./resources/**/*.js",
		"./resources/**/*.jsx",
	],
	darkMode: "selector",
	theme: {
		extend: {
			fontFamily: {
				mono: [
					'Fira Code',
					'ui-monospace',
					'SFMono-Regular',
					'Menlo',
					'Monaco',
					'Consolas',
					'"Liberation Mono"',
					'"Courier New"',
					'monospace',
				],
			},
			keyframes: {
				cursor: {
					'0%, 100%': { backgroundColor: '#000' },
					'50%': { backgroundColor: '#fff' },
				}
			},
			animation: {
				cursor: 'cursor 1.2s ease-in-out infinite',
				'spin-ultra-slow': 'spin 15s linear infinite',
			},
			boxShadow: {
				'sharp': '8px 8px 0px 5px rgba(0,0,0,0.8)',
			}
		},
	},
	plugins: [
		require('@tailwindcss/typography'),
	],
};

