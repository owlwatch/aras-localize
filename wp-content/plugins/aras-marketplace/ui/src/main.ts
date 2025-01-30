import { createApp, defineAsyncComponent, Component } from 'vue';

import './scss/index.scss';

// look for [data-aras-swiper] and implement with vue
// because we like it that way
let swiperContainers = document.querySelectorAll('[data-mp-swiper]');
let swiperPromises: Map<string, Component> = new Map();
swiperPromises.set('swiper', defineAsyncComponent(() => import('./components/Swiper.vue')));
swiperContainers?.forEach( async el => {
	const propsJSON = el.getAttribute('data-mp-swiper');
	const props = propsJSON ? JSON.parse( propsJSON ) : {};
	const AsyncComponent = swiperPromises.get('swiper');
	if( !AsyncComponent ) return;
	const app = createApp(AsyncComponent, props);
	app.mount(el);
} )