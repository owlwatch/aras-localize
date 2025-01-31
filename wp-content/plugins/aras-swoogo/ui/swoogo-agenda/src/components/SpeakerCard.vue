<!-- Script -->
<script setup lang="ts">
import type {Speaker} from '@/stores/event';
import {useEventStore} from '@/stores/event';
import {storeToRefs} from 'pinia';
import {watch} from 'vue';

const props = withDefaults(defineProps<{
	speaker: Speaker
	showButton?: boolean
}>(),{
	showButton: true
});

const {speaker} = props;

const eventStore = useEventStore();
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

const openSpeakerModal = (speaker: Speaker) => {
	eventStore.activeModalSpeaker = speaker;
};

</script>


<!-- Pug Template -->
<template lang="pug">
a.swoogo-speaker-card(
	href="#"
	@click.prevent="openSpeakerModal(speaker)"
)
	img.swoogo-speaker-card__image(
		:src="speaker.profile_picture"
		:alt="`image of ${speaker.first_name} ${speaker.last_name}`"
	)
	.swoogo-speaker-card__body
		h3.swoogo-speaker-card__name {{ speaker.first_name }} {{ speaker.last_name }}
		.swoogo-speaker-card__title {{ speaker.company }} {{ speaker.job_title }}
		
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-speaker-card {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
	width: 100%;
	background: #fff;
	border: 1px solid var(--gray-border);
	transition: 0.2s;
	&:hover {
		text-decoration: none;
		border-color: var(--red);
		box-shadow: 0 0 3px var(--red);
	}
  &__image {
    margin: 0;
    object-fit: cover;
    aspect-ratio: 1;
    width: 100%;
    height: auto;
  }
  &__body {
    padding: 1rem;
  }
	&__name {
		font-size: 1em;
	}
	&__title {
		font-size: 0.85em;
		font-weight: 400;
		color: #666;
	}
}
</style>