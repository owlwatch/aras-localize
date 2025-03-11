<!-- Script -->
<script setup lang="ts">
import type { Sponsor } from '@/stores/event';
const props = defineProps<{
	sponsor: Sponsor
}>();
</script>


<!-- Pug Template -->
<template lang="pug">
a.swoogo-sponsor-card(
	href="#"
)
	img.swoogo-sponsor-card__logo(:src="sponsor.logo_id" :alt="sponsor.name")
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
.swoogo-sponsor-card {
	// border-radius: 0.75rem;
	overflow: hidden;
	border: 1px solid var(--gray-border);
	display: flex;
	transition: 0.2s;
	height: auto;

	&__logo {
		display: block;
		margin: 0;
		padding: 0;
		object-fit: contain;
	}

	&:hover {
		border-color: var(--red);
		box-shadow: 0 0 3px var(--red);
	}
}
</style>