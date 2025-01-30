<template lang="pug">
Swiper(
	v-bind="swiperConfig"
)
	swiper-slide.swiper-slide(
		v-for="(slide, index) in slides"
		:key="index"
	)
		img(:src="slide.large || slide.image" :alt="slide.alt")
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/swiper-bundle.css'
import { Pagination, Navigation } from 'swiper/modules'

interface Slide {
	type: string
	image?: string,
	alt?: string,
	youtube_url?: string
	large?: string
}

const props = defineProps<{
	slides?: Slide[]
}>();

const swiperConfig = {
	modules: [Pagination, Navigation],
	slidesPerView: 1,
	spaceBetween: 30,
	pagination: {
		el: '.swiper-pagination',
		clickable: true
	},
	navigation: {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev'
	}
}

console.log( props.slides );

</script>

<style lang="scss" scoped>
.swiper-container {
	width: 100%;
	height: 100%;
}

.swiper-slide {
	text-align: center;
	font-size: 18px;
	background: #fff;

	/* Center slide text vertically */
	display: -webkit-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;
	-webkit-box-pack: center;
	-ms-flex-pack: center;
	-webkit-justify-content: center;
	justify-content: center;
	-webkit-box-align: center;
	-ms-flex-align: center;
	-webkit-align-items: center;
	align-items: center;
}

.swiper-pagination-bullet {
	background: #000;
}

.swiper-button-prev,
.swiper-button-next {
	color: #000;
}
</style>
