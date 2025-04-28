<!-- Script -->
<script setup lang="ts">
// components
import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';

// stores
import { useForumsStore } from '@/stores/forums';
import { ref } from 'vue';

const path = ref<string>('');
const method = ref<string>('GET');
const body = ref<string>('');
const forumsStore = useForumsStore();
const working = ref<boolean>(false);
const data = ref<any>(null);
const makeRequest = async () => {
	working.value = true;
	data.value = null;

	let json = {};

	if ( body.value ) {
		console.log(body.value)
		try {
			json = JSON.parse( body.value );
		} catch (error) {
			json = {};
		}
	}

	try {
		data.value = await forumsStore.api(path.value, json, method.value );
	} catch (error) {
		console.error('Error fetching data:', error);
	} finally {
		working.value = false;
	}
};

</script>


<!-- Pug Template -->
<template lang="pug">
.explorer
	.explorer__header
		form
			input(
				type="text"
				placeholder="enter path (for example: info)"
				v-model="path"
			)

			select(
				v-model="method"
			)
				option(value="GET") GET
				option(value="POST") POST
				option(value="PUT") PUT
				option(value="DELETE") DELETE

			button(
				type="submit"
				:disabled="working"
				@click.prevent="makeRequest"
			) {{ working ? 'Loading...' : 'Fetch' }}

			textarea(
				placeholder="Request body (JSON)"
				v-model="body"
				:disabled="working"
			)

	.explorer__data
		vue-json-pretty(
			v-if="data"
			:data="data"
			:dark="false"
			:deep="3"
			:show-line-numbers="true"
			:show-comma="false"
			:show-keys="true"
			:show-quotes="true"
			:show-colors="true"
			:show-ellipsis="false"
			:show-keys-as-strings="false"
			:show-keys-as-numbers="false"
			:show-keys-as-booleans="false"
			:show-keys-as-dates="false"
			:show-keys-as-regexps="false"
			:show-keys-as-symbols="false"
			:show-keys-as-bigints="false"
			:show-keys-as-arrays="false"
			:show-keys-as-objects="false"
			key="response"
		)
		p(v-else-if="working") Fetching data...
		p(v-else)  No data to display. Please enter a path and click GET.
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.explorer {
	display: flex;
	flex-direction: column;
	width: 100%;

	&__data {
		padding: 20px;
	}
}
form {
	width: 100%;
	display: flex;
	flex-wrap: wrap;
	gap: 0;
	input {
		flex-grow: 1;
		border: 0;
	}
	select {
		border: 0;
	}
	button {
		border: 0;
		background: #333;
		color: #fff;
	}
	> * {
		margin: 0;
	}
	textarea {
		width: 100%;
		border: 0;
		border-radius: 0;;
		border-top: 1px solid #ccc;
		font-family: monospace;
	}
}
</style>