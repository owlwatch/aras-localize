<!-- Pug Template -->
<template lang="pug">
button.glossary-letter(
	:disabled="isDisabled"
	:class="classes"
	@click="onClick"
) {{ letter }}
</template>

<!-- Script -->
<script lang="ts" setup>
import { storeToRefs } from 'pinia';
import { computed } from 'vue';
import {useGlossaryStore} from '../stores/glossary';
interface Props {
	letter: string
}

const props = defineProps<Props>();
const {letter} = props;

const glossaryStore = useGlossaryStore();
const {availableLetters} = glossaryStore;
const {activeLetter} = storeToRefs(glossaryStore);

const isActive = computed( () => activeLetter.value == letter)
const isDisabled = letter !== 'All' && !availableLetters.includes( letter );

const classes = computed( () => ({
	active: isActive.value
}));

const onClick = () => {
	if( isDisabled ){
		return;
	}
	glossaryStore.activeLetter = letter;
};
</script>

<!-- SCSS Style -->
<style lang="scss" scoped>
.glossary-letter {
	padding: 0.33em 0.4em;
	border: 1px solid #ccc;
	min-width: 1.25em;
	margin-left: -1px;
	margin-bottom: 0.5em;
	transition: 0.2s all;
	&:not(:disabled){
		cursor: pointer;
		&:hover { 
			background: #f2f2f2;
		}
	}
	&.active, &.active:hover {
		background: #1947ba;
		color: #fff;
	}
}
</style>