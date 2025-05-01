<!-- Script -->
<script setup lang="ts">
import { defineAsyncComponent, ref } from 'vue';
import { useForumsStore } from '@/stores/forums';


const pages = {
	'explorer': {
		component: defineAsyncComponent( () => import('@/components/Explorer.vue') ),
		name: 'Explorer',
	},
	
	'media': {
		component: defineAsyncComponent( () => import('@/components/Media.vue') ),
		name: 'Media',
	},

	'about': {
		component: defineAsyncComponent( () => import('@/components/About.vue') ),
		name: 'About',
	},
}
const info = ref<any>();
const activePage = ref<string>('explorer');

useForumsStore().api('info').then((response: Response) => {
	info.value = response;
}).catch((error: Error) => {
	console.error('Error fetching info:', error);
});
</script>


<!-- Pug Template -->
<template lang="pug">
.app
	.header Aras Verint UI
	.sidebar
		ul
			li(
				v-for="(page, index) in pages"
			)
				button(
					@click="activePage = index"
					:class="{ active: activePage == index }"
				) {{ page.name }}


	.main-content
		component(
			:is="pages[activePage].component"
			:key="activePage"
		)

</template>

<!-- SCSS Style -->
<style lang="scss" scoped>
.app {
	font-size: 0.9rem;
	display: grid;
	grid-template-columns: 160px 1fr;
	width: 100%;
	gap: 0px;
}
.header {
	// span 2 columns
	grid-column: 1 / -1;
	background-color: #333;
	color: #fff;
	padding: 10px;
}
.sidebar {
	width: 100%;
	justify-content: start;

	ul {
		padding: 0;
		margin: 0;
		li {
			margin: 0;
			padding: 0;
		}
	}
	
	button {
		
		width: 100%;
		padding: 10px;
		background-color: #ddd;
		color: #000;
		border: none;
		border-bottom: 1px solid #999;
		cursor: pointer;
		text-align: left;
		&.active {
			background-color: #666;
			color: #fff;
		}
	}
}
</style>