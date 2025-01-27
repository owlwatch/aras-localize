<script setup lang="ts">
import { defineProps, ref, watch, computed } from 'vue'
import { useEventStore } from './stores/event';
import type {Session, Speaker, Event} from './stores/event';
import { storeToRefs } from 'pinia';
import SessionList from './components/SessionList.vue';
import SessionModal from './components/SessionModal.vue';
import SpeakerModal from './components/SpeakerModal.vue';

const eventStore = useEventStore();
const props = defineProps<{ 
  eventId: number
  config: {
    filterByTrack: string
  }
}>();

const event = eventStore.getEvent(props.eventId);
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

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

// we want to sort these into days and times...
const sessionsByDay = ref<Record<string, Record<string, Session[]>>>({});
const filteredSessions = computed( () => {
  let all = eventStore.getEventFilteredSessions(event as Event);
  
  if( props.config.filterByTrack ){
    const filterByTrack = props.config.filterByTrack.split(',').map( (track) => track.trim().toLowerCase() );
    
    const exclude = filterByTrack.filter( (track) => track.startsWith('!') );
    const include = filterByTrack.filter( (track) => !track.startsWith('!') );
    
    all = all.filter( session => {
      // if one of the filterByTrack values starts with a ! and any
      const tracks = [eventStore.getSessionTrack(session)];
  
      if( exclude.length > 0 ){
        
        let isExcluded = exclude.some( (track:string) => {
          return tracks.some( (t) => t && testString(t.name, track.slice(1)) );
        });
        if( isExcluded ){
          return false;
        }
      }
      
      if( include.length > 0 ){
        return include.some( (track) => {
          return tracks.some( (t) => t && testString(t.name, track) );
        });
      }

      return true;
    });
  }
  return all;
});

filteredSessions.value?.forEach( (session : Session) => {


  if (!sessionsByDay.value[session.date]) {
    sessionsByDay.value[session.date] = {};
  }
  const start = new Date(session.date+' '+session.start_time);
  const end = new Date(session.date+' '+session.end_time);

  
  // time key is start time - end time 
  // in en locale with single or double digit hours
  const timeKey = `${start.toLocaleTimeString('en', {hour: 'numeric', minute: '2-digit'})} - ${end.toLocaleTimeString('en', {hour: '2-digit', minute: '2-digit'})}`;

  if (!sessionsByDay.value[session.date][timeKey]) {
    sessionsByDay.value[session.date][timeKey] = [];
  }
  sessionsByDay.value[session.date][timeKey].push(session);
});

const activeTab = ref<string>(Object.keys(sessionsByDay.value)[0]);

const sessionList = ref<InstanceType<typeof SessionList>[] | null>(null);
// watch the activeTab
watch(activeTab, (newVal) => {
  // update all visible swipers

  sessionList.value?.forEach( list => {
    list.swiperInst?.update();
  });
  
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
  const d = new Date();
  d.setFullYear(parseInt(date.split('-')[0]));
  d.setMonth(parseInt(date.split('-')[1]) - 1);
  d.setDate(parseInt(date.split('-')[2]));
  return d.toLocaleDateString('en', { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' });
};

</script>

<template lang="pug">
.swoogo-agenda
  ul.swoogo-agenda__tabs
    li.swoogo-agenda__tab(
      v-for="day in Object.keys(sessionsByDay)"
      :key="`tab-${day}`"
    )
      button(
        :id="`tab-${day}`"
        :aria-controls="`tabcontent-${day}`"
        role="tab"
        :class="{ 'is-active': day === activeTab }"
        type="button"
        @click="activeTab = day"
      ) {{ formatDate(day) }}
  
  ul.swoogo-agenda__days
    li.swoogo-agenda__day(
      role="tabpanel"
      :id="`tabccontent-${date}`"
      :aria-labelledby="`tab-${date}`"
      v-for="(times, date) in sessionsByDay"
      :key="`tabcontent-${date}`"
      :hidden="date !== activeTab"
      :class="{ 'is-active': date === activeTab }"
    )
      ul.swoogo-agenda__sessions-by-time(
        v-for="(sessions, time) in times"
        :key="time"
      )
        li.swoogo-agenda__column.swoogo-agenda__column--time {{ time }}
        li.swoogo-agenda__column.swoogo-agenda__column--sessions
          session-list(
            ref="sessionList"
            :sessions="sessions"
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
.swoogo-agenda { 
  display: flex;
  flex-direction: column;
  gap: 0.5rem;

  &__tabs {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
    justify-content: space-between;
  }
  &__tab {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex: 1;
    align-items: stretch;
    button {
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      width: 100%;
      background: #000;
      color: #fff;
      padding: 1em 1em;
      cursor: pointer;
      &:hover, &:focus {
        background-color: #333;
      }
      &.is-active {
        background: #ce2127;
      }
    }
  }
  &__days {
    margin: 0;
    padding: 0;
  }
  &__days {
    margin: 0;
    padding: 0;
  }
  &__day {
    margin: 0;
    padding: 0;
    list-style: none;
  }
  &__sessions-by-time {
    
    display: flex;
    margin: 0;
    padding: 0;
    width: 100%;
    flex-direction: column;
    @media (min-width: 768px) {
      flex-direction: row;
      border: 1px solid #ccc;
      &:not(:last-child) {
        border-bottom-width: 0;
      }
    }
  }
  &__column {
    margin: 0;
    padding: 0;
    list-style: none;
  }
  &__column--time {
    
    font-weight: 500;
    border: 1px solid #ccc;
    padding: 0.5rem 1rem;
    margin-top: 2rem;
    @media (min-width: 768px) {
      margin-top: 0;
      flex: 0 0 20ch;
      padding: 1rem;
      border-width: 0;

      background-color: transparent;
      border-right: 1px solid #ccc;
    }
  }
  &__column--sessions {
    border: 1px solid #ccc;
    border-top: 0;
    overflow: hidden;
    flex: 1;
    @media (min-width: 768px) {
      border-width: 0;
    }
  }
}
</style>
