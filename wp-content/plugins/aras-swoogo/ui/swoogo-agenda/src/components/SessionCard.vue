<!-- Script -->
<script setup lang="ts">
import type {Session, Speaker} from '@/stores/event';
import {useEventStore} from '@/stores/event';
import {storeToRefs} from 'pinia';
import {watch} from 'vue';

const props = withDefaults(defineProps<{
	session: Session
	showDate?: boolean
}>(),{
	showDate: true
});

const {session} = props;

const eventStore = useEventStore();
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

const description = session.description.replace(/<[^>]+>/g, '');
// only show the first 100 characters
const shortDescription = description.length > 100 ? description.slice(0, 100) + '...' : description;

const openSpeakerModal = (speaker: Speaker) => {
	eventStore.activeModalSpeaker = speaker;
};

function formatDate(date: string) {
	const d = new Date(date);
	return d.toLocaleDateString('en', {year: 'numeric', month: 'long', day: 'numeric'});
}

function formatTime(time: string) {
	const d = new Date(session.date + ' ' + time);
	return d.toLocaleTimeString('en', {hour: 'numeric', minute: '2-digit'});
}
</script>


<!-- Pug Template -->
<template lang="pug">
.swoogo-session-card
	.swoogo-session-card__header
		// show date
		div(v-if="props.showDate")
			strong {{ formatDate(session.date) }} {{ formatTime(session.start_time) }} - {{ formatTime(session.end_time) }}
		span.swoogo-session-card__track.swoogo-pill {{ session.track.name}} 
		a.swoogo-session-card__title(
			href="#"
			@click.prevent="activeModalSession = session"
		) {{ session.name }}

		p {{ shortDescription }}
	.swoogo-session-card__speakers
		ul
			li(v-for="speaker in session.speakers" :key="speaker.id")
				a(
					href="#"
					@click.prevent="openSpeakerModal(speaker)"
				)
					img.swoogo-session-card__speaker-image(:src="speaker.profile_picture" :alt="`image of ${speaker.first_name} ${speaker.last_name}`")
					div
						p 
							strong {{ speaker.first_name }} {{ speaker.last_name }}
						p {{ speaker.job_title }}
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-session-card {
	padding: 1rem;
	display: flex;
	flex-direction: column;
	min-height: 150px;
	width: 100%;
	&__header {
		display: flex;
		flex-direction: column;
		gap: 0.333rem;
		flex-grow: 1;
	}
	&__title {
		font-size: 1.05rem;
		font-weight: 500;
		color: #000;
	}
	&__speakers {
		container: speakers / inline-size;
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