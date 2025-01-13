import { createApp, defineAsyncComponent } from 'vue'
import type {AsyncComponentLoader, Component} from 'vue';
import { createPinia } from 'pinia'
import { useEventStore } from './stores/event';

import './assets/main.scss';

let widgets = document.querySelectorAll('[data-aras-widget^="swoogo-"]');

let widgetMap = {
	'agenda' : () => import ('./Agenda.vue'),
	'speakers' : () => import ('./Speakers.vue'),
	'sponsors' : () => import ('./Sponsors.vue'),
}
let widgetPromises: Map<string, Component> = new Map();

widgetPromises.set('agenda', defineAsyncComponent(() => import('./Agenda.vue')));
widgetPromises.set('speakers', defineAsyncComponent(() => import('./Speakers.vue')));
widgetPromises.set('sponsors', defineAsyncComponent(() => import('./Sponsors.vue')));

widgets?.forEach( async el => {
	// const widget type
	const widgetType = el.getAttribute('data-aras-widget')?.replace('swoogo-', '');
	if( !widgetType ) return;

	const configJSON = el.getAttribute('data-config');
	const config = configJSON ? JSON.parse( configJSON ) : {};

	const json = el.textContent?.trim();
	if( !json ) return;
	const data = JSON.parse( json );

	// we will want to create a new app for each of the widgets
	let AsyncComponent = widgetPromises.get(widgetType);
	if( !AsyncComponent ) return;
	
	const app = createApp(AsyncComponent, {
		eventId: data.details.id,
		config
	});

	app.use(createPinia());
	
	// setup our stores
	const eventStore = useEventStore();
	eventStore.addEvent( data );

	// remove the loader for this widget
	const loading = el.nextSibling;

	if( loading && loading instanceof HTMLElement && loading.classList?.contains('swoogo-loading') ){
		loading.remove();
	}

	let container = document.createElement('div');
	el.parentElement?.insertBefore(container, el.nextSibling);
	app.mount(container);
	
} )
// import App from './App.vue'

// const app = createApp(App)

// app.use(createPinia())

// app.mount('#app')
