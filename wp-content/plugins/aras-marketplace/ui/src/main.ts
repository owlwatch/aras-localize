import { createApp, defineAsyncComponent, Component } from 'vue';

import './scss/index.scss';

// look for [data-aras-swiper] and implement with vue
// because we like it that way
let swiperContainers = document.querySelectorAll('[data-mp-solution-gallery]');
let swiperPromises: Map<string, Component> = new Map();
swiperPromises.set('SolutionGallery', defineAsyncComponent(() => import('./components/SolutionGallery.vue')));
swiperContainers?.forEach( async el => {
	const propsJSON = el.getAttribute('data-mp-solution-gallery');
	const props = propsJSON ? JSON.parse( propsJSON ) : {};
	const AsyncComponent = swiperPromises.get('SolutionGallery');
	if( !AsyncComponent ) return;
	const app = createApp(AsyncComponent, props);
	app.mount(el);
} )