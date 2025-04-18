<!-- Script -->
<script setup lang="ts">
import { ref } from 'vue';
// extend window typescript with "wpApiSettings" object
declare global {
	interface Window {
		wpApiSettings: {
			root: string;
			nonce: string;
			version: string;
			siteUrl: string;
			apiUrl: string;
		};
	}
}

const info = ref<any>();

// lets get the info for example
const {root, nonce} = window.wpApiSettings;

// lets fetch the data from the api
const fetchData = async () => {
	const response = await fetch(`${root}aras-verint/v1/api/info`, {
		headers: {
			'X-WP-Nonce': nonce,
		},
	})
	const data = await response.json()
	info.value = data;
}

fetchData();
</script>


<!-- Pug Template -->
<template lang="pug">
h1 Verint app
pre {{ info }}
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>

</style>