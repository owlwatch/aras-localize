<!-- Script -->
<script setup lang="ts">
import {useScrollLock, onKeyStroke} from '@vueuse/core';
import { onMounted, onUnmounted, ref} from 'vue';

const emit = defineEmits(['close']);
const props = withDefaults(defineProps<{
	showCloseButton?: boolean,
	fadeIn?: boolean
}>(), {
	showCloseButton: true,
	fadeIn: true
});

const {fadeIn} = props;

const modal = ref<HTMLElement | null>(null);
const container = ref<HTMLElement | null>(null);

const windowScrollLock = useScrollLock(window);
const modalLocked = useScrollLock(modal);

onMounted(() => {
	setTimeout(() => {
		windowScrollLock.value = true;
		modalLocked.value = false;
	}, 10);
});

onUnmounted(() => {
	windowScrollLock.value = false;
	modalLocked.value = false;
});

onKeyStroke('Escape', (e) => {
	emit('close');
})

function clickOutside(event: Event){
	if(event.target === modal.value || event.target === container.value){
		emit('close');
	}
}

</script>


<!-- Pug Template -->
<template lang="pug">
teleport(to="body")
	.swoogo-modal(
		ref="modal"
		@click="clickOutside"
		:class="{ 'swoogo-modal--fade-in': fadeIn }"
	)
		.swoogo-modal__container.grid-container(
			ref="container"
		)
			.swoogo-modal__dialog
				.swoogo-modal__header
					slot(name="header")
						h3 Modal Header
					
					// close button
					slot(name="close" v-if="props.showCloseButton")
						
						button.close-button(
							aria-label="Close modal"
							type="button"
							@click="$emit('close')"
						)
							span(aria-hidden="true") &times;

				.swoogo-modal__body
					slot(name="body")
						h3 Modal Body
					
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-modal {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	min-height: 100%;
	z-index: 10000;
	display: block;
	overflow: scroll	;
	background-color: rgba(0, 0, 0, 0.6);
	backdrop-filter: blur(5px);
	&--fade-in {
		animation: fade-in 0.1s ease-in-out;
	}
	&__container {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: flex-start;
		min-height: 100%;
		padding-top: 3rem;
		padding-bottom: 3rem;
	}
	&__dialog {
		min-height: 100%;
		width: 100%;
		max-width: 900px;
		position: relative;
		z-index: 2;
		margin-left: auto;
		margin-right: auto;
		background-color: #fff;
		border-radius: 0.5rem;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
	}
	&__header {
		display: flex;
		flex-direction: column;
		gap: 0.333rem;
		flex-grow: 1;
		// position: sticky;
		// top: 3rem;
		padding: 2rem 2rem 0.5rem;
		background: #fff;
		border-radius: 0.5rem 0.5rem 0 0;
	}
	
	&__body {
		padding: 1rem 2rem 2rem !important;
	}
}

@keyframes fade-in {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}
</style>