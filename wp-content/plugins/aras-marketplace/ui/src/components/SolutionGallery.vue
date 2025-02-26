<template lang="pug">
.mp-solution-gallery
	.mp-solution-gallery__large
		Swiper.mp-solution-gallery__large-swiper(
			v-bind="largeSwiperConfig"
			:thumbs="{swiper: thumbsSwiper}"
			@activeIndexChange="activeIndexChange"
			@swiper="setLargeSwiper"
		)
			swiper-slide.swiper-slide(
				v-for="(slide, index) in media"
				:key="index"
				@click="openModal(index)"
			)
				img(:src="slide.large || slide.image" :alt="slide.alt")
	.mp-solution-thumbs
		Swiper(
			v-bind="thumbsSwiperConfig"
			@swiper="setThumbsSwiper"
		)
			swiper-slide.swiper-slide.swiper-slide--thumb(
				v-for="(slide, index) in media"
				:key="index"
			)
				img(
					@click="setActiveIndex(index)"
					:src="slide.large || slide.image" :alt="slide.alt"
				)

	// Modal
teleport(to="body")
	.modal(v-if="isModalOpen")
		.modal-content
			.modal-close(@click="closeModal") 
				svg(
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 24 24"
					width="24"
					height="24"
					fill="currentColor"
				)
					path(
						d="M18.3 5.71a1 1 0 00-1.42 0L12 10.59 7.12 5.71a1 1 0 00-1.42 1.42L10.59 12l-4.89 4.88a1 1 0 001.42 1.42L12 13.41l4.88 4.89a1 1 0 001.42-1.42L13.41 12l4.89-4.88a1 1 0 000-1.41z"
					)
			template( v-if="media[modalIndex].youtube_url" )
				iframe.mp-solution-gallery__youtube(
					:src="getYoutubeEmbedUrl(media[modalIndex].youtube_url)"
					frameborder="0"
					width="16"
					height="9"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
					allowfullscreen
				)
			template(v-else)
				img(:src="media[modalIndex].large || media[modalIndex].image" :alt="media[modalIndex].alt")
			.modal-nav
				button.modal-nav.modal-prev(
					@click="prevSlide"
					type="button"
					:disabled="!hasPrev()"
				) ←
				button.modal-nav.modal-next(
					@click="nextSlide"
					type="button"
					:disabled="!hasNext()"
				) →
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/swiper-bundle.css'
import { Pagination, Navigation, Controller, Thumbs } from 'swiper/modules'

interface Slide {
	type: string
	image?: string,
	alt?: string,
	youtube_url?: string
	large?: string
}

const props = defineProps<{
	media?: Slide[]
}>();

const media = ref(props.media || []);

const thumbsSwiper = ref<typeof Swiper | null>(null);
const largeSwiper = ref<typeof Swiper | null>(null);
const isModalOpen = ref(false);
const modalIndex = ref(0);

const setThumbsSwiper = (swiper: any) => {
	thumbsSwiper.value = swiper;
}

const setLargeSwiper = (swiper: any) => {
	largeSwiper.value = swiper;
}

const setActiveIndex = (index: number) => {
	if( largeSwiper.value ){
		largeSwiper.value.slideTo(index);
	}
}

const activeIndexChange = (index: number) => {
	if( thumbsSwiper.value ){
		thumbsSwiper.value.slideTo(index);
	}
}

const openModal = (index: number) => {
	modalIndex.value = index;
	isModalOpen.value = true;
}

const closeModal = () => {
	isModalOpen.value = false;
}

const prevSlide = () => {
	if (hasPrev()) {
		modalIndex.value--;
		setActiveIndex(modalIndex.value);
	}
}

const nextSlide = () => {
	if (hasNext()) {
		modalIndex.value++;
		setActiveIndex(modalIndex.value);
	}
}

const hasNext = () => {
	return modalIndex.value < media.value.length - 1;
}

const hasPrev = () => {
	return modalIndex.value > 0;
}

const getYoutubeEmbedUrl = ( url: string ) => {
	// allow for all types of youtube urls
	const youtubeId = url.split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/)[2];
	const youtubeEmbedUrl = `https://www.youtube.com/embed/${youtubeId}?autoplay=1`;
	return youtubeEmbedUrl;
}

const largeSwiperConfig = {
	modules: [Navigation, Thumbs],
	slidesPerView: 1,
	spaceBetween: 30,
	navigation: true
}

const thumbsSwiperConfig = {
	modules: [Thumbs],
	slidesPerView: 'auto',
	spaceBetween: 20,
  watchSlidesProgress: true
}
</script>

<style lang="scss" scoped>
.swiper-container {
	width: 100%;
	height: 100%;
}

.mp-solution-gallery {
	display: flex;
	flex-direction: column;
	gap: 1rem;

	&__large-swiper {
		--swiper-navigation-size: 20px;
		--swiper-theme-color: var(--mp-color-brand);
		.swiper-slide {
			cursor: zoom-in;
			display: flex;
			align-items: center;
			justify-content: center;
			height: auto;
			background: #f2f2f2;
		}
	}
	// &:deep(.swiper-button-prev),
	// &:deep(.swiper-button-next) {
		
	// }
	&__youtube {
		max-width: 100%;
		width: 900px;
		height: auto;
		aspect-ratio: 16/9;
		border-width: 0;
	}
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
	&--thumb {
		width: 80px;
		height: 80px;
		border: 2px solid #ccc;
		img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
	}
	&-thumb-active {
		border-color: var(--mp-color-brand)
	}
}

.swiper-pagination-bullet {
	background: #000;
}

.swiper-button-prev,
.swiper-button-next {
	color: #000;
}

.modal {
	position: fixed;
	z-index: 1000;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.8);
	display: flex;
	justify-content: center;
	align-items: center;
	&-content {
		position: relative;
		background: #fff;
		padding: 1rem;
		max-width: calc(100% - 2rem);
		max-height: calc(100% - 4rem);
		overflow: visible;
	}
	&-close {
		position: absolute;
		bottom: calc( 100% );
		right: 0;
		cursor: pointer;
		
		color: #fff;
	}
	&-nav {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		font-size: 2rem;
		line-height: 1;
		color: var(--mp-color-brand);
		cursor: pointer;
		&:disabled {
			opacity: 0.3;
		}
		&.modal-prev {
			margin-right: auto;
		}
		&.modal-next {
			margin-left: auto;
		}
	}
}
</style>
