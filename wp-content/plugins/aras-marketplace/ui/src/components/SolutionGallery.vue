<template lang="pug">
.mp-solution-gallery
	
	.mp-solution-gallery__large
		Swiper.mp-solution-gallery__large-swiper(
			v-bind="largeSwiperConfig"
			:thumbs="{swiper: thumbsSwiper}"
			@activeIndexChange="activeIndexChange"
			@swiper="setLargeSwiper"
		)
			swiper-slide.swiper-slide.swiper-slide--large(
				v-for="(slide, index) in media"
				:class="`swiper-slide--${slide.type}`"
				:key="index"
				@click="openModal(index)"
			)
				img(:src="slide.large || slide.image" :alt="slide.alt")
				// display a play button over the image if the type is a video
				.swiper-slide__video-overlay(
					v-if="slide.type === 'video'"
				)
					svg.swiper-slide__play-button(
						v-if="slide.type === 'video'"
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 384 512"
					)
						path(
							fill="currentColor"
							d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z"
						)
	
	.mp-solution-gallery__thumbs

		Swiper(
			v-bind="thumbsSwiperConfig"
			@swiper="setThumbsSwiper"
		)
			SwiperSlide.swiper-slide.swiper-slide--thumb(
				v-for="(slide, index) in media"
				:key="index"
			)
				img(
					@click="setActiveIndex(index)"
					:src="slide.large || slide.image" :alt="slide.alt"
				)
				// display a play button over the image if the type is a video
				.swiper-slide__video-overlay(
					v-if="slide.type === 'video'"
				)
					svg.swiper-slide__play-button(
						v-if="slide.type === 'video'"
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 384 512"
					)
						path(
							fill="currentColor"
							d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z"
						)

// Modal
teleport(to="body")
	.mp-solution-gallery__modal(v-if="isModalOpen")
		.mp-solution-gallery__modal-buttons
			button.mp-solution-gallery__modal-prev(
				@click="prevSlide"
				type="button"
				:disabled="!hasPrev()"
			)
				svg(
					xmlns="http://www.w3.org/2000/svg"
					width="16"
					height="16"
					fill="currentColor"
					class="bi bi-chevron-left"
					viewBox="0 0 16 16"
				)
					path(
						fill-rule="evenodd"
						d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"
					)
				
			button.mp-solution-gallery__modal-prev(
				@click="nextSlide"
				type="button"
				:disabled="!hasNext()"
			)
				svg(
					xmlns="http://www.w3.org/2000/svg"
					width="16"
					height="16"
					fill="currentColor"
					class="bi bi-chevron-right"
					viewBox="0 0 16 16"
				)
					path(
						fill-rule="evenodd"
						d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"
					)
			button.mp-solution-gallery__modal-close(@click="closeModal") 
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
					
			iframe.mp-solution-gallery__modal-content.mp-solution-gallery__modal-content--youtube(
				:src="getYoutubeEmbedUrl(media[modalIndex].youtube_url)"
				frameborder="0"
				width="16"
				height="9"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			)
		template(v-else)
					
			img.mp-solution-gallery__modal-content.mp-solution-gallery__modal-content--image(
				:src="media[modalIndex].large || media[modalIndex].image" :alt="media[modalIndex].alt"
			)
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/swiper-bundle.css'
import { Navigation, Thumbs } from 'swiper/modules'

import { onMounted, onBeforeUnmount } from 'vue';

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
	const end = url.split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/)[2];
	const youtubeId = end !== undefined ? end.split(/[^0-9a-z_\-]/i)[0] : end;
	const youtubeEmbedUrl = `https://www.youtube.com/embed/${youtubeId}?autoplay=1`;
	return youtubeEmbedUrl;
}

const largeSwiperConfig = {
	modules: [Thumbs],
	slidesPerView: 'auto',
	spaceBetween: 30,
	autoHeight: true,
	mousewheel: {
		enabled: true
	}
}

const thumbsSwiperConfig = {
	modules: [Thumbs, Navigation],
	slidesPerView: 'auto',
	spaceBetween: 20,
	centeredSlides: true,
	centeredSlidesBounds: true,
	slideToClickedSlide: true,
	// navigation: {
	// 	nextEl: '.mp-solution-gallery__thumbs-nav--next',
	// 	prevEl: '.mp-solution-gallery__thumbs-nav--prev',

	// },
  watchSlidesProgress: true
}

// modal stuff
const handleOutsideClick = (event: MouseEvent) => {
	if (isModalOpen.value && !(event.target as HTMLElement).closest('.mp-solution-gallery__modal-content, .mp-solution-gallery__modal-buttons')) {
		closeModal();
	}
};
watch(isModalOpen, (val) => {
	if (val) {
		setTimeout(() => window.addEventListener('click', handleOutsideClick), 1);	
		document.body.style.overflow = 'hidden';
	} else {
		window.removeEventListener('click', handleOutsideClick);
		document.body.style.overflow = '';
	}
});

const handleKeydown = (event: KeyboardEvent) => {
	if (isModalOpen.value) {
		if (event.key === 'Escape') {
			closeModal();
		} else if (event.key === 'ArrowLeft') {
			prevSlide();
		} else if (event.key === 'ArrowRight') {
			nextSlide();
		}
	}
};

onMounted(() => {
	window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
	window.removeEventListener('keydown', handleKeydown);
});
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
	margin: 1rem 0;

	--swiper-navigation-size: 20px;
	--swiper-theme-color: var(--mp-color-brand);

	&__thumbs {
		display: flex;
		gap: 10px;
		align-items: center;
		position: relative;
		height: 100px;
		width: 100%;
		> .swiper {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
		}
	}

	&__nav {
		display: flex;
		flex-direction: row;
		gap: 1rem;
		align-self: center;
		width: 100%;
		justify-content: space-between;
		&-button {
			font-size: 1rem;
			border-radius: 50%;
			height: 1.5em;
			width: 1.5em;
			color: inherit;
			display: flex;
			align-items: center;
			justify-content: center;
			background: #ccc;
			color: #000;
			cursor: pointer;
			svg {
				width: 60%;
				height: 60%;
			}
			&:hover:not(:disabled) {	
				background-color: var(--mp-color-brand);
				color: #fff;
			}
			&:disabled {
				opacity: 0.2;
			}
			&--prev svg {
				transform: translateX(-5%);
			}
			&--next svg {
				transform: translateX(10%);
			}
		}
	}

	&__large {
		aspect-ratio: 16/9;
		position: relative;
		&-swiper {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			.swiper-wrapper {

			}
		}

	}

	&__large-swiper {
		.swiper-slide {
			cursor: zoom-in;
			display: flex;
			align-items: center;
			justify-content: center;
			height: 100%;
			width: auto;
			> img {
				width: auto;
				max-height: 100%;
			}
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

	&__modal {
		position: fixed;
		z-index: 1000;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.9);
		backdrop-filter: blur(4px);
		display: flex;
		justify-content: center;
		align-items: center;
		&-buttons {
			z-index: 100;
			position: absolute;
			top: 3rem;
			right: 1rem;
			display: flex;
			justify-content: space-between;
			padding: 0.35rem;
			gap: 0.5em;
			background-color: rgba(0, 0, 0, .15);
			box-shadow: rgba(255,255,255,0.3) 0px 4px 16px;
			border-radius: 50px;
			button {
				color: #fff;
				display: flex;
				align-items: center;
				justify-content: center;
				width: 1.75em;
				height: 1.75em;
				border-radius: 50%;
				background: #333;
				&:hover {
					background: #282828;
					box-shadow: rgba(255,255,255,0.3) 0px 2px 4px;
				}
				&:disabled {
					opacity: 0.25;
				}
			}
		}
		
		&-content {
			position: relative;
			background: #fff;
			max-width: calc(100% - 2rem);
			max-height: calc(100% - 4rem);
			overflow: visible;
			display: flex;
			flex-direction: column;
			width: auto;
			height: auto;
			&--youtube {
				width: 900px;
				height: auto;
				aspect-ratio: 16/9;
			}
		}
	}
}

.swiper-slide {
	max-width: 100%;
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

	
	&__video-overlay {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		// radial gradient background
		background: radial-gradient(circle, rgba(255,255,255,0.6) 0%, rgba(255,255,255,0.1) 100%);
	}
	&__video-overlay:hover &__play-button {
		transform: translate(-50%, -50%) scale(1.1);
	}
	&__play-button {
		transition: 0.2s all;
		width: 20%;
		aspect-ratio: 1/1;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		color: var(--mp-color-brand);
		filter: drop-shadow(0 0 20px rgba(255,255,255,1));
		z-index: 2;
		path {
			stroke: #fff;
		}
	}
	&--thumb &__play-button {
		width: 40%;
	}
}


.swiper-pagination-bullet {
	background: #000;
}

.swiper-button-prev,
.swiper-button-next {
	color: #000;
}


</style>
