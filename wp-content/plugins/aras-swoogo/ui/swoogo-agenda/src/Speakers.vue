<script setup lang="ts">
import { defineProps, ref, watch, computed } from 'vue'
import { useEventStore } from './stores/event';
import type {Session, Speaker, Event} from './stores/event';
import { storeToRefs } from 'pinia';
import SpeakerModal from './components/SpeakerModal.vue';
import SessionModal from './components/SessionModal.vue';
import SpeakerCard from './components/SpeakerCard.vue';

const eventStore = useEventStore();
const props = defineProps<{ 
  eventId: number
  config: any
}>();

const event = eventStore.getEvent(props.eventId);
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

const speakers = computed( () => eventStore.getEventSpeakers(event as Event) );

const isModalOpen = ref<boolean>(false);
let modalTimeout: number | null = null;

watch( activeModalSession, (val) => {

  if( val ){
    modalTimeout = setTimeout( () => {
      isModalOpen.value = true;
    }, 20);
  }
  else {
    modalTimeout = setTimeout( () => {
      isModalOpen.value = false;
    }, 10);
  }

  if (val && activeModalSpeaker.value ) {
    activeModalSpeaker.value = null;
  }
});

watch( activeModalSpeaker, (val) => {
  if( val ){
    modalTimeout = setTimeout( () => {
      isModalOpen.value = true;
    }, 20);
  }
  else {
    modalTimeout = setTimeout( () => {
      isModalOpen.value = false;
    }, 10);
  }
  if (val && activeModalSession.value ) {
    activeModalSession.value = null;
  }
});

const formatDate = (date: string) => {
  const d = new Date(date);
  return d.toLocaleDateString('en', { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' });
};

</script>

<template lang="pug">
.swoogo-speaker-list
  ul.swoogo-speaker-list__list
    li.swoogo-speaker-list__list-item(
      v-for="speaker in speakers"
      :key="`speaker-${speaker.id}`"
    )
      speaker-card(
        :speaker="speaker"
      )

speaker-modal(
  v-if="activeModalSpeaker"
  :fadeIn="!isModalOpen"
  :key="`speaker-modal-${activeModalSpeaker.id}`"
  :speaker="activeModalSpeaker"
  @close="activeModalSpeaker = null"
)

session-modal(
  v-if="activeModalSession"
  :fadeIn="!isModalOpen"
  :key="`modal-${activeModalSession.id}`"
  :session="activeModalSession"
  @close="activeModalSession = null"
)
</template>

<style scoped lang="scss">
.swoogo-speaker-list { 
  display: flex;
  flex-direction: column;
  gap: 0.5rem;

  &__list {
		padding: 0;
		margin: 0;
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 2rem;
	}
	&__list-item {
		list-style: none;
		display: flex;
	}
  &__speaker {
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
  }
  &__speaker-image {
    margin: 0;
    object-fit: cover;
    aspect-ratio: 1;
    width: 100%;
    height: auto;
  }
  &__speaker-body {
    padding: 1rem;
  }
}
</style>
