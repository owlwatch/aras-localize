<!-- Script -->
<script setup lang="ts">
import type { Sponsor } from '@/stores/event';
const props = defineProps<{
	sponsor: Sponsor
}>();
</script>


<!-- Pug Template -->
<template lang="pug">
.swoogo-sponsor-row
	.swoogo-sponsor-row__image
		img.swoogo-sponsor-row__logo(:src="sponsor.logo_id" :alt="sponsor.name")

	.swoogo-sponsor-row__content
		h3.swoogo-sponsor-row__name
			a(
				:href="sponsor.website"
				target="_blank"
			) {{ sponsor.name }}
		.swoogo-sponsor-row__description(v-html="sponsor.description")
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-sponsor-row {

	display: grid;
	grid-template-columns: 200px 1fr;
	gap: 3rem;

	@container sponsor-list ( max-width: 500px ){
		grid-template-columns: 1fr;
		gap: 1rem;
	}
	&__image {
		text-align: center;
	}
	&__logo {
		margin: 0;
		max-width: 200px;
	}
	&__description {
		*:last-child {
			margin-bottom: 0;
		}
	}
}
</style>