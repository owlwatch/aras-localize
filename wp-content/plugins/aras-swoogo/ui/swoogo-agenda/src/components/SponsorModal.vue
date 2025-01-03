<!-- Script -->
<script setup lang="ts">
import type { Sponsor } from '@/stores/event';
import {useEventStore} from '@/stores/event';
import {onKeyStroke} from '@vueuse/core';
import { onMounted, onUnmounted, ref, toRefs, computed } from 'vue';
import Modal from './Modal.vue';

const props = defineProps<{ sponsor: Sponsor }>();
const emit = defineEmits(['close']);

const {sponsor} = props;
const eventStore = useEventStore();


function getSponsorLevelKey(level: string) {
  // split the level name, first word will be the css color name
  const [color] = level.split(' ');
  return color.toLowerCase();
}
</script>


<!-- Pug Template -->
<template lang="pug">
modal(
	@close="emit('close')"
	:show-close-button="true"
)
	template(v-slot:header)
		
		span.swoogo-sponsor-modal__level.swoogo-pill(
			:style="{color: `var(--color-${getSponsorLevelKey(sponsor.level?.value)})`}"
		) {{ sponsor.level?.value }}
		// modal header
		h2.swoogo-sponsor-modal__title
			a(
				:href="sponsor.website"
			) {{ sponsor.name }}

		
	template(v-slot:body)
		.swoogo-sponsor-modal__body
			.swoogo-sponsor-modal__main
				.swoogo-sponsor-modal__bio(v-html="sponsor.description")
				a.swoogo-sponsor-modal__video(
					v-if="sponsor.video_url"
					:href="sponsor.video_url"
					target="_blank"
				)
					img(
						src="@/assets/video-placeholder.jpg"
						alt="Watch Video"
					)
					
			.swoogo-sponsor-modal__sidebar
				img.swoogo-sponsor-modal__logo(
					:src="sponsor.logo_id"
					:alt="sponsor.name"
				)

				a(
					:href="sponsor.website"
				) Visit Website

				// contact information
				.swoogo-sponsor-modal__contact-information
					.swoogo-sponsor-modal__contact-name {{ sponsor.c_29712 }}
					.swoogo-sponsor-modal__contact-email 
						a(:href="`mailto:${sponsor.c_29713}`") {{ sponsor.c_29713 }}
					.swoogo-sponsor-modal__contact-phone {{ sponsor.c_29714 }}
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-sponsor-modal {
	
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

	&__body {
		display: flex;
		flex-direction: column;
		gap: 3rem;
		@media (min-width: 768px) {
			flex-direction: row;
		}
	}

	&__logo {
		border-radius: 0.75rem;
		border: 1px solid #ccc;
	}
	&__main {
		width: 100%;
		flex-grow: 1;
	}
	&__sidebar {
		display: flex;
		flex-direction: column;
		gap: 1rem;
		@media (min-width: 768px) {
			flex: 0 0 280px;
		}
	}

	&__contact-information {
		margin-top: 2rem;
		font-weight: 300;
	}
	&__contact-name {
		font-weight: 500;
	}
	
}
</style>