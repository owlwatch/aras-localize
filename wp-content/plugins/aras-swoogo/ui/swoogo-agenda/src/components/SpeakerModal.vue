<!-- Script -->
<script setup lang="ts">
import type { Session, Speaker } from '@/stores/event';
import {useEventStore} from '@/stores/event';
import {onKeyStroke} from '@vueuse/core';
import { onMounted, onUnmounted, ref, toRefs, computed } from 'vue';
import Modal from './Modal.vue';
import SessionCard from './SessionCard.vue';

const props = defineProps<{ speaker: Speaker }>();
const emit = defineEmits(['close']);

const {speaker} = props;
const eventStore = useEventStore();

const sessions = computed( () => eventStore.getSessionsBySpeaker(speaker) );
</script>


<!-- Pug Template -->
<template lang="pug">
modal(
	@close="emit('close')"
	:show-close-button="true"
)
	template(v-slot:header)
		
		.swoogo-speaker-modal__header
			.swoogo-speaker-modal__profile-pic
				img(
					:src="speaker.profile_picture"
					:alt="speaker.first_name + ' ' + speaker.last_name"
				)
			.swoogo-speaker-modal__name-and-info
				// modal header
				h2.swoogo-speaker-modal__name {{ speaker.first_name }} {{ speaker.last_name }}

				// speaker job title
				div.swoogo-speaker-modal__company-and-title
					div.swoogo-speaker-modal__label-and-value.swoogo-speaker-modal__company
						span.swoogo-speaker-modal__label Company:
						span.swoogo-speaker-modal__value {{ speaker.company }}
					div.swoogo-speaker-modal__label-and-value.swoogo-speaker-modal__title
						span.swoogo-speaker-modal__label Job Title:
						span.swoogo-speaker-modal__value {{ speaker.job_title }}

	template(v-slot:body)
		p.swoogo-speaker-modal__bio(
			v-if="speaker.bio"
			v-html="speaker.bio"
		)
		h4 Sessions
		ul.swoogo-speaker-modal__sessions-list
			li(v-for="session in sessions" :key="session.id")
					session-card(
						:session="session"
						:show-speakers="false"
					)
			
		
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-speaker-modal {
	&__header {
		display: flex;
		align-items: flex-end;
		gap: 1rem;
	}
	&__profile-pic {
		aspect-ratio: 1;
		object-fit: contain;
		object-position: 50% 50%;
		height: 100px;
	}
	&__sessions-list {
		padding: 0;
		margin: 0;
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
		gap: 1rem;
		li {
			list-style: none;
			display: flex;
			border: 1px solid #ccc;
		}
	}

	&__company-and-title {
		display: flex;
		flex-wrap: wrap;
		column-gap: 2rem;
		row-gap: 0.5rem;
	}

	&__label-and-value {
		display: flex;
		flex-direction: column;
	}
	
	&__label {
		color: #666;
		font-size: 0.85rem;
		font-weight: 400;
	}

	&__value {
		font-weight: 500;
	}
	
}
</style>