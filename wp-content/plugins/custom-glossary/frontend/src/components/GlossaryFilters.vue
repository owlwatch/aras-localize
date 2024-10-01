<!-- Pug Template -->
<template lang="pug">
.glossary-filters.grid-x
	.cell.small-12.large-9
		.glossary-letters
			glossary-letter(
				v-for="letter of letters"
				:letter="letter"
			) {{ letter }}
	.cell.small-12.large-3
		input(
			type="text"
			placeholder="Search..."
			v-model="searchTerm"
		)
</template>

<!-- Script -->
<script lang="ts" setup>
import { storeToRefs } from 'pinia';
import { watch } from 'vue';
import {useGlossaryStore} from '../stores/glossary';

import GlossaryLetter from './GlossaryLetter.vue';

// find the terms
const glossaryStore = useGlossaryStore();

const {letters, activeLetter, searchTerm} = storeToRefs(glossaryStore);

watch( activeLetter, (letter) => {
	if( letter !== 'All' ) {
		searchTerm.value = '';
	}
	glossaryStore.rows.forEach( (row) => {
		(row.element as HTMLElement).style.display = (letter == 'All' || row.letter == letter)
			? ''
			: 'none'
	});
});

watch( searchTerm, (term) => {
	if( term ){
	
		activeLetter.value = 'All';
		searchTerm.value = term;
	}
	// lets search all the rows
	const search = term.toLowerCase();
	glossaryStore.rows.forEach( (row) => {
		
		const match = row.term.toLowerCase().match( search ) || row.excerpt.toLowerCase().match(search);
		(row.element as HTMLElement).style.display = match
			? ''
			: 'none'
	});
});

</script>

<!-- SCSS Style -->
<style lang="scss" scoped>
.glossary-filters {
	margin-bottom: 2em;
}
.glossary-letters {
	padding-left: 1px;
	display: inline-flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: center;
}
</style>