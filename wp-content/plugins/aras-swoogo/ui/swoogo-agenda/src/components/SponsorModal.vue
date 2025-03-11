<!-- Script -->
<script setup lang="ts">
import type { Sponsor } from '@/stores/event';
import {useEventStore} from '@/stores/event';
import {onKeyStroke} from '@vueuse/core';
import { onMounted, onUnmounted, ref, toRefs, computed } from 'vue';
import Modal from './Modal.vue';

import videoPlaceholderUrl from '@/assets/images/video-placeholder.jpg';

const props = defineProps<{
	sponsor: Sponsor
	showLevel: boolean
}>();
const emit = defineEmits(['close']);

const {sponsor, showLevel} = props;
const eventStore = useEventStore();


function getSponsorLevelKey(level: string) {
  // split the level name, first word will be the css color name
  const [color] = level.split(' ');
  return color.toLowerCase();
}

/**
 * If the passed url is any version of a youtube url,
 * return the associated embed url
 * 
 * @param url 
 * @return url
 */
function getYoutubeEmbedUrl( url: string ){
	const youtubeRegex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/;
	const match = url.match(youtubeRegex);
	if( match ){
		return `https://www.youtube.com/embed/${match[1]}`;
	}
	return false;
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
			v-if="showLevel"
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
				div(
					v-if="sponsor.video_url"
				)
					// embed the youtube video if possible
					iframe.swoogo-sponsor-modal__video-embed(
						v-if="getYoutubeEmbedUrl(sponsor.video_url)"
						:src="getYoutubeEmbedUrl(sponsor.video_url)"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					)

					a.swoogo-sponsor-modal__video-link(
						v-else
						:href="sponsor.video_url"
						target="_blank"
					)
						img(
							src="/images/video-placeholder.jpg"
							alt="Watch Video"
						)
						
			.swoogo-sponsor-modal__sidebar
				img.swoogo-sponsor-modal__logo(
					:src="sponsor.logo_id"
					:alt="sponsor.name"
				)

				a(
					:href="sponsor.website"
					target="_blank"
				) Visit Website

				.swoogo-sponsor-modal__contact-download(
					v-if="sponsor.company_asset"
				)
					a.aras-button(
						aria-label="Download PDF"
						target="_blank"
						:href="sponsor.company_asset || '#'"
					) Download PDF

				// contact information
				.swoogo-sponsor-modal__contact-information
					.swoogo-sponsor-modal__contact-name(
						v-if="sponsor.primary_contact"
					) {{ sponsor.primary_contact }}

					.swoogo-sponsor-modal__contact-email(
						v-if="sponsor.primary_contact_email"
					)
						a(:href="`mailto:${sponsor.primary_contact_email}`") {{ sponsor.primary_contact_email }}
					.swoogo-sponsor-modal__contact-phone(
						v-if="sponsor.primary_contact_phone"
					) {{ sponsor.primary_contact_phone }}
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
		
	}

	&__video-embed {
		width: 100%;
		aspect-ratio: 16/9;;
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
		// margin-top: 2rem;
		font-weight: 300;
	}
	&__contact-name {
		font-weight: 500;
	}
	
}
</style>