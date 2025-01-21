<script setup lang="ts">
import { defineProps, ref, watch, computed } from 'vue'
import { useEventStore } from './stores/event';
import type {Session, Speaker, Event, Sponsor} from './stores/event';
import { storeToRefs } from 'pinia';
import SponsorModal from './components/SponsorModal.vue';

const eventStore = useEventStore();
const props = defineProps<{
  eventId: number
  config?: {
    useSponsorLevels: boolean,
    filterByLevel: string
  }
}>();

const event = eventStore.getEvent(props.eventId);
const {activeModalSession, activeModalSpeaker} = storeToRefs(eventStore);

const sponsors = computed( () => {
  let all = eventStore.getEventSponsors(event as Event);

  if( props.config?.filterByLevel ){
    // filterByLevel is a comma separated case insensitive string
    const levels = props.config.filterByLevel.split(',').map( (level) => level.trim().toLowerCase() );
    all = all.filter( (sponsor) => {
      return sponsor.level && levels.includes(sponsor.level.value.toLowerCase())
    });
  }

  // sort them by sponsor level.value The level.value is a string
  // that contains bronze, silver, gold and platinum, sort them in that order
  const sortOrder = [/bronze/i,/silver/i,/gold/i,/platinum/i];
  if( !props.config?.useSponsorLevels ){
    return all.sort( (a,b) => a.name.localeCompare(b.name) );
  }
  return all.sort( (a,b) => {
    const aLevel = a.level?.value || 'bronze';
    const bLevel = b.level?.value || 'bronze';
    const aIndex = sortOrder.findIndex( (re) => re.test(aLevel) );
    const bIndex = sortOrder.findIndex( (re) => re.test(bLevel) );
    if (aIndex === bIndex) {
      return a.name.localeCompare(b.name);
    }
    return aIndex - bIndex;
  }).reverse();
});;


const sponsorsByLevel = computed( () => {
  const levels = sponsors.value.map( (sponsor) => sponsor.level?.value ).filter( (level) => level );
  // get unique values
  const uniqueLevels = Array.from(new Set(levels));
  return uniqueLevels.map( (level) => {
    return {
      level,
      sponsors: sponsors.value.filter( (sponsor) => sponsor.level?.value === level )
    };
  });
});

// this will take a singular label name and return the plural
function sponsorLevelLabel(level: string) {
  return level+'s';
}

function getSponsorLevelKey(level: string) {
  // split the level name, first word will be the css color name
  const [color] = level.split(' ');
  return color.toLowerCase();
}

const activeModalSponsor = ref<Sponsor|null>(null);

</script>

<template lang="pug">
.swoogo-sponsors(
  v-if="config?.useSponsorLevels"
)
  ul.swoogo-sponsors__levels
    li.swoogo-sponsors__level(
      v-for="group in sponsorsByLevel"
      :key="`${group.level}`"
      :style="{'--level-color': `var(--color-${getSponsorLevelKey(group.level)})`, '--level-card-width': `var(--width-${getSponsorLevelKey(group.level)})`}"
    )
      h2.swoogo-sponsors__level-title(
      ) {{ sponsorLevelLabel(group.level) }}
      .swoogo-sponsors__sponsor
      ul.swoogo-sponsors__sponsors
        li.swoogo-sponsors__sponsor(
          v-for="sponsor in group.sponsors"
          :key="`sponsor-${sponsor.id}`"
        )
          a.swoogo-sponsor-card(
            href="#"
            @click.prevent="activeModalSponsor=sponsor"
          )
            img.swoogo-sponsor-card__logo(:src="sponsor.logo_id" :alt="sponsor.name")
            //- h3.swoogo-sponsor-card__name {{ sponsor.name }}

ul.swoogo-sponsors__sponsors(
  v-else
)
  li.swoogo-sponsors__sponsor(
    v-for="sponsor in sponsors"
    :key="`sponsor-${sponsor.id}`"
  )
    a.swoogo-sponsor-card(
      href="#"
      @click.prevent="activeModalSponsor=sponsor"
    )
      img.swoogo-sponsor-card__logo(:src="sponsor.logo_id" :alt="sponsor.name")
      //- h3.swoogo-sponsor-card__name {{ sponsor.name }}

sponsor-modal(
  v-if="activeModalSponsor"
  :key="`sponsor-modal-${activeModalSponsor.id}`"
  :sponsor="activeModalSponsor"
  :showLevel="config?.useSponsorLevels"
  @close="activeModalSponsor = null"
)
</template>

<style scoped lang="scss">
.swoogo-sponsors { 

  --width-platinum: 300px;
  --width-bronze: 200px;

  @media (max-width: 768px) {
    --width-gold: 150px;
    --width-silver: 150px;
    --width-bronze: 150px;
  }

  &__levels {
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 4rem;
  }
  &__level {
    display: flex;
    margin: 0;
    padding: 0;
    list-style: none;
    flex-direction: column;
    gap: 1rem;
  }
  &__level-title {
    color: var(--level-color, #000);
    font-size: 3rem;
    font-weight: 700;
    text-align: center;
    @media (max-width: 768px) {
      font-size: 2rem;
    }
  }
  &__sponsors {
    display: grid;
    gap: 2rem;
    margin: 0;
    padding: 0;
    grid-template-columns: repeat(auto-fill, minmax(var(--level-card-width, 200px), 1fr));
    @media (max-width: 768px) {
      gap: 1rem;
    }
  }
  &__sponsor {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    height: auto;
  }
}

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
