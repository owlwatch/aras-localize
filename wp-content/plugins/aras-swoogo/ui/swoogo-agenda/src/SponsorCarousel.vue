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

import SponsorModal from './components/SponsorModal.vue';

const eventStore = useEventStore();
const props = defineProps<{
  eventId: number,
  config?: {
    filterByLevel: string
  }
}>();

const event = eventStore.getEvent(props.eventId);

const sponsors = computed( () => {
  
  let all = eventStore.getEventSponsors(event as Event);
  if( props.config?.filterByLevel ){
    // filterByLevel is a comma separated case insensitive string
    const levels = props.config.filterByLevel.split(',').map( (level) => level.trim().toLowerCase() );
    all = all.filter( (sponsor) => {
      return sponsor.level && levels.includes(sponsor.level.value.toLowerCase())
    });
  }
  return all;
});

const breakpoints = {
  450: {
    slidesPerView: 3,
    spaceBetween: 40
  },
  600: {
    slidesPerView: 4,
    spaceBetween: 40
  },
  750: {
    slidesPerView: 5,
    spaceBetween: 40
  },
  900: {
    slidesPerView: 6,
    spaceBetween: 40
  },
  850: {
    slidesPerView: 7,
    spaceBetween: 40
  }
}

const swiperOptions = {
  slidesPerView: 2,
  spaceBetween: 40,
  // centeredSlides: true,
  autoplay: {
    delay: 1200,
    disableOnInteraction: false,
    pauseOnMouseEnter: true
  },
  speed: 1000,
  simulateTouch: false,
  allowTouchMove: false,
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

const activeModalSponsor = ref<Sponsor|null>(null);
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
      a(
        href="#"
        @click.prevent="activeModalSponsor=sponsor"
      )
        img(
          :src="sponsor.logo_id"
          :alt="sponsor.name"
        )

sponsor-modal(
  v-if="activeModalSponsor"
  :key="`sponsor-modal-${activeModalSponsor.id}`"
  :sponsor="activeModalSponsor"
  :showLevel="false"
  @close="activeModalSponsor = null"
)
</template>

<style scoped lang="scss">
@use "sass:color";
.swoogo-sponsor-carousel { 
  &:deep(.swiper-wrapper) {
    // transition-timing-function: linear;
  }
  &__swiper-slide {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100px;
    img {
      max-height: 100px;
      width: 100%;
      object-fit: contain;
      object-position: 50% 50%;
    }
  }
}
</style>