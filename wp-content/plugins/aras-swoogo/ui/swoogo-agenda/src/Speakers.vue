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
  config: {
    filterByTrack: string
  }
}>();

const event = eventStore.getEvent(props.eventId);
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

const speakers = computed( () => {

  // utility function to test a string against another string that can contain
  // * as a wildcard
  const testString = (str: string, test: string) => {
    str = str.toLowerCase();
    test = test.toLowerCase();
    if( test === '*' ){
      return true;
    }
    if( test.startsWith('*') ){
      return str.endsWith(test.slice(1));
    }
    if( test.endsWith('*') ){
      return str.startsWith(test.slice(0,-1));
    }
    return str === test;
  };

  let all = eventStore.getEventSpeakers(event as Event);
  if( props.config.filterByTrack ){
    all = all.filter( (speaker) => {
      // get all the tracks for the speaker...
      const sessions = eventStore.getSessionsBySpeaker(speaker);
      let tracks = sessions.map( (session) => {
        return eventStore.tracks.find( t => t.id == session.track_id )?.name;
      });
      
      const filterByTrack = props.config.filterByTrack.split(',').map( (track) => track.trim().toLowerCase() );

      const exclude = filterByTrack.filter( (track) => track.startsWith('!') );
      const include = filterByTrack.filter( (track) => !track.startsWith('!') );
      // if one of the filterByTrack values starts with a ! and any
      
      if( exclude.length > 0 ){
        
        let isExcluded = exclude.some( (track) => {
          return tracks.some( (t) => t && testString(t, track.slice(1)) );
        });
        if( isExcluded ){
          return false;
        }
      }
      
      if( include.length > 0 ){
        return include.some( (track) => {
          return tracks.some( (t) => t && testString(t, track) );
        });
      }
      return true;
    });
  }

  all.sort( (a, b) => {
    return a.last_name.localeCompare(b.last_name);
  });
  return all;
});

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
		grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
		gap: 1rem;
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
