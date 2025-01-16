<script setup lang="ts">
import { defineProps, ref, watch, computed } from 'vue'
import { useEventStore } from './stores/event';
import type {Speaker, Event} from './stores/event';
import { storeToRefs } from 'pinia';
import SpeakerCard from './components/SpeakerCard.vue';

// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Mousewheel } from 'swiper/modules';
import type { Swiper as SwiperInst } from 'swiper/types';

// Import Swiper styles
import 'swiper/css';

const eventStore = useEventStore();
const props = defineProps<{
  eventId: number,
  config: any
}>();

const event = eventStore.getEvent(props.eventId);
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

const speakers = computed( () => eventStore.getEventSpeakers(event as Event) );

const breakpoints = {
  500: {
    slidesPerView: 2,
    spaceBetween: 20
  },
  800: {
    slidesPerView: 3,
    spaceBetween: 20
  },
  1100: {
    slidesPerView: 4,
    spaceBetween: 30
  }
}

const swiperOptions = {
  slidesPerView: 1,
  spaceBetween: 20,
  breakpoints,
  navigation: true,
  modules: [Mousewheel]
}

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

const swiperInst = ref<SwiperInst | null>(null);
const showNav = ref(false);

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

</script>

<template lang="pug">
.swoogo-speaker-carousel(
  :class="{ 'swoogo-speaker-carousel--nav': showNav }"
)
  button.swoogo-speaker-carousel__nav.swoogo-speaker-carousel__nav--prev(
    @click="swiperInst?.slidePrev()"
    :disabled="isBeginning"
  )
    // chevron left as an svg
    svg(xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512")
      path(
        d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"
      )

  swiper.swoogo-speaker-carousel__swiper(
    v-bind="swiperOptions"
    @swiper="swiperInst = $event"
  )
    swiper-slide.swoogo-speaker-carousel__swiper-slide(
      v-for="speaker in speakers"
      :key="speaker.id"
    )
      speaker-card(
        :speaker="speaker"
      )

  button.swoogo-speaker-carousel__nav.swoogo-speaker-carousel__nav--next(
    @click="swiperInst?.slideNext()"
    :disabled="isEnd"
  )
    svg(xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512")
      path(
        d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z")
</template>

<style scoped lang="scss">
@use "sass:color";
.swoogo-speaker-carousel { 
  display: flex;
  gap: 0.5rem;
  width: 100%;
  &__swiper-slide {
    display: flex;
    height: auto;
    padding-top:5px;
    padding-bottom:5px;
  }
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
}
</style>
