<!-- Script -->
<script setup lang="ts">
import { ref, watch } from 'vue';
import SessionCard from './SessionCard.vue';
import type {Session} from '@/stores/event';

// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Mousewheel } from 'swiper/modules';
import type { Swiper as SwiperInst } from 'swiper/types';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/scrollbar';

const props = defineProps<{
	sessions: Session[]
}>();

const swiperInst = ref<SwiperInst | null>(null);
const swiperModules = [Mousewheel];
const showNav = ref(false);

const useSlider = props.sessions.length > 1;

const isBeginning = ref(true);
const isEnd = ref(false);

watch( swiperInst, (swiper) => {
	swiper?.on('lock', () => showNav.value = false );
	swiper?.on('unlock', () => showNav.value = true );
	
	swiper?.on('update', () => {
		if( !swiper?.isLocked ){
			showNav.value = true;
		}
		else {
			showNav.value = false;
		}
	});

	swiper?.on('transitionEnd', () => {
		isBeginning.value = swiper?.isBeginning;
		isEnd.value = swiper?.isEnd;
	});

	swiper?.on('transitionStart', () => {
		isBeginning.value = swiper?.isBeginning;
		isEnd.value = swiper?.isEnd;
	});

});

defineExpose({ swiperInst });
</script>

<!-- Pug Template -->
<template lang="pug">
template(
	v-if="useSlider"
)
	.swoogo-sessions-list(
		:class="{ 'swoogo-sessions-list--nav': showNav }"
	)
		button.swoogo-sessions-list__nav.swoogo-sessions-list__nav--prev(
			@click="swiperInst?.slidePrev()"
			:disabled="isBeginning"
		)
			// chevron left as an svg
			svg(xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512")
				path(
					d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"
				)
			
		swiper.swoogo-sessions-list__swiper(
			slidesPerView="auto"
			:centeredSlides="false"
			:setWrapperSize="false"
			:mousewheel="{enabled:true, forceToAxis:true}"
			:modules="swiperModules"
			@swiper="swiperInst = $event"
		)
			swiper-slide.swoogo-sessions-list__swiper-slide(
				v-for="session in sessions"
				:key="session.id"
			)
				session-card(
					:showDate="false"
					:session="session"
				)
		button.swoogo-sessions-list__nav.swoogo-sessions-list__nav--next(
			@click="swiperInst?.slideNext()"
			:disabled="isEnd"
		)
			svg(xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512")
				path(
					d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z")
template(v-else)
	session-card(
		:showDate="false"
		:session="sessions[0]"
	)
</template>


<!-- SCSS Style -->
<style lang="scss" scoped>
@use "sass:color";
.swoogo-sessions-list {
	display: flex;
	width: 100%;
	&__nav {
		background: #ce2127;
		color: #fff;
		display: none;
		padding: 0.1rem 0.25rem;
		align-items: center;
		cursor: pointer;
		&:hover, &:focus {
			background: color.adjust( #ce2127, $lightness: -20% );
		}
		&:disabled {
			background: #ddd;
		}
		svg {
			width: 10px;
		}
		path {
			fill: currentColor;
		}
	}
	&--nav &__nav {
		display: flex;
	}

	&__swiper {
		flex-grow: 1;
		margin: 0;
		padding: 0;
		display: flex;
	}
	&__swiper-slide {
		margin: 0;
		padding: 0;
		list-style: none;
		flex: 1 1 auto;
		display: flex;
		width: 100%;
		min-width: 270px;
		height: auto;
		&:not(:last-child) {
			border-right: 1px solid #ccc;
		}
	}
}
</style>