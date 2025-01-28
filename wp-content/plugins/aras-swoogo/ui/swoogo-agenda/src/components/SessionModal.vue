<!-- Script -->
<script setup lang="ts">
import type { Session } from '@/stores/event';
import {useEventStore} from '@/stores/event';
import {onKeyStroke} from '@vueuse/core';
import { onMounted, onUnmounted, ref, toRefs, computed } from 'vue';
import Modal from './Modal.vue';

const props = defineProps<{ session: Session }>();
const emit = defineEmits(['close']);

// get next and previous sessions if they exist
const eventStore = useEventStore();
const {session} = toRefs(props);
const next = computed<Session|null>( () => eventStore.getNextSession(session.value) );
const previous = computed<Session|null>( () => eventStore.getPreviousSession(session.value) );

onKeyStroke('ArrowRight', (e) => {
	if(next.value){
		eventStore.setActiveModalSession(next.value);
	}
});

onKeyStroke('ArrowLeft', (e) => {
	if(previous.value){
		eventStore.setActiveModalSession(previous.value);
	}
});

const location = computed( () => eventStore.getSessionLocation(session.value) );

// format the date and time
const formatDate = (date: string) => {
	const d = new Date(date);
	return d.toLocaleDateString('en', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};

// format start_time and end_time
const formatTime = (time: string) => {
	const t = new Date(session.value.date+' '+time);
	return t.toLocaleTimeString('en', { hour: 'numeric', minute: '2-digit' });
};

</script>


<!-- Pug Template -->
<template lang="pug">
modal(@close="emit('close')" :show-close-button="false")
	template(v-slot:header)
		
		.swoogo-pill {{ session.track.name }}
		// modal header
		h2 {{ session.name }}

		.swoogo-session-modal__location 
			// font awesome svg for map
			svg(
				xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 576 512"
			)
				path(
					d="M302.8 312C334.9 271.9 408 174.6 408 120C408 53.7 354.3 0 288 0S168 53.7 168 120c0 54.6 73.1 151.9 105.2 192c7.7 9.6 22 9.6 29.6 0zM416 503l144.9-58c9.1-3.6 15.1-12.5 15.1-22.3L576 152c0-17-17.1-28.6-32.9-22.3l-116 46.4c-.5 1.2-1 2.5-1.5 3.7c-2.9 6.8-6.1 13.7-9.6 20.6L416 503zM15.1 187.3C6 191 0 199.8 0 209.6L0 480.4c0 17 17.1 28.6 32.9 22.3L160 451.8l0-251.4c-3.5-6.9-6.7-13.8-9.6-20.6c-5.6-13.2-10.4-27.4-12.8-41.5l-122.6 49zM384 255c-20.5 31.3-42.3 59.6-56.2 77c-20.5 25.6-59.1 25.6-79.6 0c-13.9-17.4-35.7-45.7-56.2-77l0 194.4 192 54.9L384 255z")
			
			span {{ location.name }}

		// date and time
		div.swoogo-session-modal__date-bar
			div.swoogo-session-modal__date
				strong {{ formatDate(session.date) }} {{ formatTime(session.start_time) }} - {{ formatTime(session.end_time) }}
			div.swoogo-session-modal__nav
				// previous session
				button.swoogo-session-modal__nav-button(
					type="button"
					:disabled="!previous"
					@click="eventStore.activeModalSession = previous"
				) ←
				// next session
				button.swoogo-session-modal__nav-button(
					type="button"
					:disabled="!next"
					@click="eventStore.activeModalSession = next"
				) →

				// close session
				button.swoogo-session-modal__nav-button.swoogo-session-modal__nav-button--close(
					type="button"
					@click="emit('close')"
				)
					// font awesome svg for x
					svg(width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg")
						path(d="M18 6L6 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round")
						path(d="M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round")

	template(v-slot:body)
		.swoogo-session-modal__body
			// modal body
			.swoogo-session-modal__description(
				v-html="session.description"
			)
			.swoogo-session-modal__speakers(
				v-if="session.speakers.length"
			)
				h4 Speakers
				ul
					li(v-for="speaker in session.speakers" :key="speaker.id")
						a(
							href="#"
							@click.prevent="eventStore.activeModalSpeaker = speaker"
						)
							img.swoogo-session-modal__speaker-image(
								v-if="speaker.profile_picture"
								:src="speaker.profile_picture" :alt="`image of ${speaker.first_name} ${speaker.last_name}`"
							)
							div
								div 
									strong {{ speaker.first_name }} {{ speaker.last_name }}
								div {{ speaker.job_title }}
					
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-session-modal {
	
	&__date-bar {
		order: -1;
		display: flex;
		justify-content: space-between;
		margin-bottom: 0.25em;
	}
	&__location {
		display: flex;
		align-items: center;
		gap: 0.25em;
		svg {
			width: 1.2em;
			path {
				fill: currentColor;
			}
		}
	}
	&__nav {
		display: flex;
		gap: 0.5rem;
		&-button {
			background: #000;
			color: #fff;
			font-weight: 500;
			display: flex;
			padding: 0.2em 0.5em;
			align-items: center;
			justify-content: center;
			&:hover, &:focus {
				background: var(--red);
				color: #fff;
			}
			&:disabled {
				color: #fff;
				background-color: #ddd;
			}
			&--close {
				border: 2px solid #666;
				background: #fff;
				border-radius: 50%;
				color: #000;
				aspect-ratio: 1;
				margin-left: 1rem;
				cursor: pointer;
				svg {
					width: 0.75em;
					height: 0.75em;
					path {
						stroke: currentColor;
					}
				}
				&:hover {
					border-color: var(--red);
				}
			}
		}
	}
	&__body {
		display: flex;
		gap: 3rem;
		flex-direction: column;
	}
	&__description {
		flex: 1;
	}
	&__speakers {
		container: speakers / inline-size;
		h4 {
			margin-bottom: 1em;
		}
		ul {
			display: grid;
			gap: 0.75em;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			padding: 0;
			margin: 0;
		}
		li {
			list-style: none;
			a {
				color: #000;
				display: flex;
				gap: 1rem;
				align-items: flex-start;
			}
		}
		p {
			margin-bottom: 0;
			font-size: 0.9rem;
		}
	}
	&__speaker-image {
		flex-shrink: 0;
		width: 50px;
		height: 50px;
		object-fit: cover;
		border-radius: 50%;
		margin: 0;
	}
}
</style>