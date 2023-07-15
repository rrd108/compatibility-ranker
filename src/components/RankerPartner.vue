<script setup lang="ts">
  import { PropType } from 'vue'
  import getIcon from '@/composables/useIcon.ts'
  import { useStore } from '@/store'
  import RankerPartnerAnalysis from '@/components/RankerPartnerAnalysis.vue'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import isMale from '@/composables/useIsMale'

  defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })

  const store = useStore()

  const isAgeDifferenceBig = (ageDifference: number) => {
    // the man should be older maximum 10 years or younger maximum 4 years
    if (isMale(store.targetPerson.sex)) {
      if (ageDifference >= -10 && ageDifference <= 4) {
        return false
      }
    }
    if (!isMale(store.targetPerson.sex)) {
      if (ageDifference >= -4 && ageDifference <= 10) {
        return false
      }
    }
    return true
  }
</script>

<template>
  <div v-if="partner">
    <h2>
      {{ getIcon(partner.sex) }} {{ partner.name }}
      <font-awesome-icon icon="link" @click="TODOanalize(partner.id)" />
    </h2>

    <h3>
      {{ partner.birth_date }} {{ partner.birth_time }},
      {{ partner.birth_place }}
    </h3>

    <h4>
      <font-awesome-icon icon="moon" />
      {{ store.zodiacs[partner.moon] }} {{ partner.moon }}
    </h4>

    <h4>
      <font-awesome-icon icon="meteor" />
      {{ partner.naksatra }}, {{ partner.pada }}
      <small class="outRange" v-if="!store.naksatras.includes(partner.naksatra)"
        >Unknown Naksatra</small
      >
    </h4>

    <ul>
      <li>
        <h6>{{ Math.floor((partner.points / store.maxPoints) * 100) }}%</h6>
        ({{ partner.points }} points)
      </li>
      <li>
        <RankerPartnerAnalysis :partner="partner" />
      </li>
      <li :class="{ outRange: isAgeDifferenceBig(partner.age_difference) }">
        <h6>{{ partner.age_difference }}</h6>
        év korkülönbség
      </li>
    </ul>
  </div>
</template>

<style scoped>
  ul {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-gap: 0.5em;
    margin: 0;
    align-items: normal;
    width: 100%;
  }
  li {
    background-color: #0c7c59;
    padding: 1em;
    margin: 0.25em 0;
    color: #fff;
    width: 100%;
    text-align: center;
    font-size: 0.75rem;
  }
  h6 {
    font-size: 10rem;
    font-weight: normal;
    padding: 0.25em;
  }
</style>
