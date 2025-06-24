/** @type {import('vite').UserConfig} */
import { fileURLToPath, URL } from 'node:url'
import { defineConfig, searchForWorkspaceRoot } from 'vite'

import inject from "@rollup/plugin-inject";

export default defineConfig({

	plugins: [
		inject({
			$:'jquery',
			jQuery:'jquery'
		})
	],

	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./src', import.meta.url))
		}
	},
	
	base: './' ,

	build: {
		manifest: true,
		emptyOutDir: true,
		outDir: '../dist',
		rollupOptions: {
			input: [
				'./src/index.js',
				'./src/admin.scss',
				'./src/login.scss'
			]
		}
	},

	server:{
		port: 5176,
		cors : true,
		origin: 'http://localhost:5176',
		strictPort: true,
		fs: {
			allow: [
				fileURLToPath(new URL('../../', import.meta.url)),
			]
		}
	},

	css: {
		preprocessorOptions: {
			scss: {
				api: 'modern-compiler' // or "modern"
			}
		}
	},
})