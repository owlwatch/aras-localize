<script setup lang="ts">
import { ref, watch, computed, provide } from 'vue'
import { useEventStore } from './stores/event';
import type {Session, Event} from './stores/event';
import { storeToRefs } from 'pinia';
import SessionModal from './components/SessionModal.vue';
import SpeakerModal from './components/SpeakerModal.vue';


const eventStore = useEventStore();


const props = defineProps<{ 
  eventId: number
  config: {
    filterByTrack: string,
    hideTrack?: boolean,
    hideDateAndTime?: boolean,
    ink?: string,
    muted?: string,
    border?: string,
    card?: string,
    accent?: string,
    column1?: string,
    column2?: string,
  }
}>();


const hideDateAndTime = props.config.hideDateAndTime || false;
provide('hideDateAndTime', hideDateAndTime);

const event = eventStore.getEvent(props.eventId);
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

const hideTrack = props.config.hideTrack || false;
provide('hideTrack', hideTrack);

const agendaStyles = computed(() => {
  const styles: Record<string, string> = {};
  const setVar = (name: string, value?: string) => {
    if (value && value.trim() !== '') {
      styles[name] = value.trim();
    }
  };
  setVar('--agenda-ink', props.config.ink);
  setVar('--agenda-muted', props.config.muted);
  setVar('--agenda-border', props.config.border);
  setVar('--agenda-card', props.config.card);
  setVar('--agenda-accent', props.config.accent);
  return styles;
});

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

watch(activeTab, () => {
  // reserved for future tab-change side-effects
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

const formatTabDay = (date: string) => {
  const d = new Date(date + ' 00:00:00');
  return d.toLocaleDateString('en', { weekday: 'short' });
};

const formatTabDate = (date: string) => {
  const d = new Date(date + ' 00:00:00');
  return d.toLocaleDateString('en', { month: 'short', day: 'numeric' });
};

const shortDescriptionFor = (session: Session) => {
  const raw = session.description || '';
  const text = raw.replace(/<[^>]+>/g, '');
  return text.length > 160 ? text.slice(0, 160) + '...' : text;
};

</script>

<template lang="pug">
.swoogo-agenda(:style="agendaStyles")
  ul.swoogo-agenda__tabs(
    v-if="!hideDateAndTime"
  )
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
      )
        span.swoogo-agenda__tab-day {{ formatTabDay(day) }}
        span.swoogo-agenda__tab-date {{ formatTabDate(day) }}
  
  ul.swoogo-agenda__days(
    v-if="!hideDateAndTime"
  )
    li.swoogo-agenda__day(
      role="tabpanel"
      :id="`tabccontent-${date}`"
      :aria-labelledby="`tab-${date}`"
      v-for="(times, date) in sessionsByDay"
      :key="`tabcontent-${date}`"
      :hidden="date !== activeTab"
      :class="{ 'is-active': date === activeTab }"
    )
      ul.swoogo-agenda__list
        template(
          v-for="(sessions, time) in times"
        )
        
        
          li.swoogo-agenda__card(
            v-for="session in sessions"
            :key="session.id"
          )
            a.swoogo-agenda__card-link(
              href="#"
              @click.prevent="activeModalSession = session"
            )
              span.swoogo-agenda__tag(
                v-if="session.track && !hideTrack"
              ) {{ session.track.name }}
              span.swoogo-agenda__title {{ session.name }}
              p.swoogo-agenda__description(
                v-if="shortDescriptionFor(session)"
              ) {{ shortDescriptionFor(session) }}
              .swoogo-agenda__time
                svg.swoogo-agenda__time-icon(
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                )
                  path(
                    d="M12 1.75c5.66 0 10.25 4.59 10.25 10.25S17.66 22.25 12 22.25 1.75 17.66 1.75 12 6.34 1.75 12 1.75Zm0 1.5a8.75 8.75 0 1 0 0 17.5 8.75 8.75 0 0 0 0-17.5Zm.75 4.5v4.4l3.2 2.1-.82 1.23-3.88-2.53V7.75h1.5Z"
                  )
                span {{ time }}
  // If hideDateAndTime is true, show all sessions in a single list
  ul.swoogo-agenda__days(
    v-else
  )
    li.swoogo-agenda__day
      ul.swoogo-agenda__list
        li.swoogo-agenda__card(
          v-for="session in filteredSessions"
          :key="session.id"
        )
          a.swoogo-agenda__card-link(
            href="#"
            @click.prevent="activeModalSession = session"
          )
            span.swoogo-agenda__tag(
              v-if="session.track && !hideTrack"
            ) {{ session.track.name }}
            span.swoogo-agenda__title {{ session.name }}
            p.swoogo-agenda__description(
              v-if="shortDescriptionFor(session)"
            ) {{ shortDescriptionFor(session) }}
        

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

  --agenda-ink: #1f1b16;
  --agenda-muted: #6d6760;
  --agenda-border: #e3dfd7;
  --agenda-card: #ffffff;
  --agenda-accent: #cf4b2e;
  --agenda-columns: 2;

  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  padding: 1.5rem 0;
  border-radius: 20px;
  container: agenda / inline-size;

  &__tabs {
    display: flex;
    gap: 0.75rem;
    list-style: none;
    margin: 0;
    padding: 0;
    overflow-x: auto;
    overflow-y: clip;
    scrollbar-width: thin;
    justify-content: center;
    border-bottom: 1px solid var(--agenda-border);
    padding-bottom: 0.5rem;
    
  }

  & &__tabs {
    font-size: 1rem;
    @container( max-width: 800px ){
      gap: 0;
      font-size: 0.8rem;
    }
  }

  &__tab {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: stretch;
    font-size: inherit;
    button {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      min-width: 120px;
      background: #fff;
      color: var(--agenda-ink);
      padding: 0.6em 1.25em;
      border: 0;
      border-radius: 0;
      box-shadow: none;
      cursor: pointer;
      gap: 0.15em;
      transition: color 160ms ease;
      &:hover, &:focus {
        color: var(--agenda-accent);
      }
      &.is-active {
        color: var(--agenda-ink);
      }

      @container( max-width: 800px ){
        min-width: auto;
        padding: 0.6em 1em;
      }
    }
  }
  &__tab-day {
    font-size: 0.9em;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
  }
  &__tab-date {
    font-size: 1.5em;
    font-weight: 600;
  }
  &__tab-day {
    font-size: 0.9em;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
  }
  &__tab-date {
    font-size: 1.5em;
    font-weight: 600;
  }
  &__tab button {
    position: relative;
  }
  &__tab button::after {
    transition: all 160ms ease;
    content: "";
    position: absolute;
    z-index: 2;
    left: 0%;
    right: 0%;
    bottom: -0.55rem;
    height: 5px;
    border-radius: 999px;
  }
  &__tab button:hover::after,
  &__tab button:focus::after {
    background: rgba(0,0,0,0.5);
  }
  &__tab button.is-active::after,
  &__tab button.is-active:hover::after,
  &__tab button.is-active:focus::after {
    
    background: var(--agenda-accent);
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
  &__list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 1.25rem;
    grid-template-columns: repeat(var(--agenda-columns), 1fr);
    @container ( max-width: 800px ) {
      grid-template-columns: 1fr;
    }
  }
  &__group {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  &__cards {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 1rem;
    height: 100%;
  }
  &__card {
    list-style: none;
    margin: 0;
    padding: 1.25rem 1.5rem;
    background: var(--agenda-card);
    border: 1px solid var(--agenda-border);
    border-radius: 18px;
    box-shadow: 0 14px 30px rgba(24, 20, 16, 0.06);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: transform 140ms ease, box-shadow 140ms ease;
    min-height: 100%;
  }
  &__card-link {
    display: flex;
    flex-direction: column;
    height: 100%;
    gap: 0.75rem;
    color: inherit;
    text-decoration: none;
  }
  &__card:hover,
  &__card:focus-within {
    transform: scale(0.985);
    box-shadow: none;
  }
  &__tag {
    background: color-mix(in srgb, var(--agenda-accent) 18%, transparent);
    color: var(--agenda-accent);
    border-radius: 999px;
    padding: 0.2rem 0.7rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    width: fit-content;
  }
  &__title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--agenda-ink);
    text-decoration: none;

  }
  &__title:hover,
  &__title:focus {
    text-decoration: underline;
  }
  &__description {
    color: var(--agenda-muted);
    font-size: 0.98rem;
    line-height: 1.5;
    margin: 0;
  }
  &__time {
    margin-top: auto;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--agenda-muted);
    font-size: 1.1rem;
    font-weight: 400;
  }
  &__time-icon {
    width: 1.5em;
    height: 1.5em;
    fill: var(--agenda-accent);
  }

  @media (min-width: 900px) {
    &__list {
      gap: 1.5rem;
    }
    &__card {
      padding: 1.5rem 1.75rem;
    }
  }
}
</style>
