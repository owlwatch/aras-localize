<!-- Script -->
<script setup lang="ts">
import { ref } from 'vue';
import { useForumsStore } from '@/stores/forums';
import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';

const forumsStore = useForumsStore();

const data = ref<any>(null);

forumsStore.rest('media-objects').then((response: Response) => {
	data.value = response;
}).catch((error: Error) => {
	console.error('Error fetching media objects:', error);
});
</script>


<!-- Pug Template -->
<template lang="pug">
vue-json-pretty(
	v-if="data"
	:data="data"
	:dark="true"
	:deep="3"
)
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>

</style>