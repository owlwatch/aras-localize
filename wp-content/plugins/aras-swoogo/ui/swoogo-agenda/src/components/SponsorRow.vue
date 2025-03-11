<!-- Script -->
<script setup lang="ts">
import type { Sponsor } from '@/stores/event';
const props = defineProps<{
	sponsor: Sponsor
}>();
</script>


<!-- Pug Template -->
<template lang="pug">
.swoogo-sponsor-row(
	href="#"
)
	.swoogo-sponsor-row__image
		img.swoogo-sponsor-row__logo(:src="sponsor.logo_id" :alt="sponsor.name")

	.swoogo-sponsor-row__content
		h3.swoogo-sponsor-row__name {{ sponsor.name }}
		div(v-html="sponsor.description")
</template>


<!-- SCSS Style -->
<style lang="scss" scoped></style>