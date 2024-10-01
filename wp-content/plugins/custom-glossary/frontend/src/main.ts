import { createApp } from 'vue'
import { createPinia } from 'pinia'
import GlossaryFilters from './components/GlossaryFilters.vue';

const app = createApp(GlossaryFilters);
const pinia = createPinia();

app.use(pinia);

const el = document.querySelector('[data-glossary-filters]');
if( el ){
	app.mount(el);
}
