<script setup lang="ts">
import { defineProps, ref, watch, computed } from 'vue'
import { useEventStore } from './stores/event';
import type {Sponsor, Event} from './stores/event';
import { storeToRefs } from 'pinia';

// Import Swiper Vue.js components
import { Swiper, SwiperSlide} from 'swiper/vue';
import { Mousewheel, Autoplay } from 'swiper/modules';
import type { Swiper as SwiperInst } from 'swiper/types';

// Import Swiper styles
import 'swiper/css';

const eventStore = useEventStore();
const props = defineProps<{
  eventId: number,
  config: any
}>();

const event = eventStore.getEvent(props.eventId);

const sponsors = computed( () => eventStore.getEventSponsors(event as Event) );

const breakpoints = {
  
}

const swiperOptions = {
  slidesPerView: 'auto',
  spaceBetween: 40,
  centeredSlides: true,
  autoplay: {
    delay: 0,
    disableOnInteraction: false,
  },
  speed: 3000,
  loop: true,
  breakpoints,
  navigation: true,
  modules: [Mousewheel, Autoplay]
}

const isModalOpen = ref<boolean>(false);
let modalTimeout: number | null = null;

const swiperInst = ref<SwiperInst | null>(null);
const showNav = ref(false);

const isBeginning = ref(true);
const isEnd = ref(false);

watch( swiperInst, (swiper) => {});

</script>

<template lang="pug">
.swoogo-sponsor-carousel
  
  swiper.swoogo-sponsor-carousel__swiper(
    v-bind="swiperOptions"
    @swiper="swiperInst = $event"
  )
    swiper-slide.swoogo-sponsor-carousel__swiper-slide(
      v-for="sponsor in sponsors"
      :key="sponsor.id"
    )
      img(
        :src="sponsor.logo_id"
        :alt="sponsor.name"
      )
</template>

<style scoped lang="scss">
@use "sass:color";
.swoogo-sponsor-carousel { 
  &:deep(.swiper-wrapper) {
    transition-timing-function: linear;
  }
  &__swiper-slide {
    display: flex;
    height: auto;
    width: 180px;
    aspect-ratio: 16 / 9;
    img {
      height: 100%;
      width: 100%;
      object-fit: contain;
      object-position: 50% 50%;
    }
  }
}
</style>
