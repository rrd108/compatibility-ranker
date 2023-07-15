<script setup lang="ts">
  import getIcon from '@/composables/useIcon.ts'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import { useStore } from '@/store'
  import { PropType } from 'vue'
  import isMale from '@/composables/useIsMale'

  const props = defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })

  const store = useStore()

  const rasiInfo = [
    {
      difference: [0],
      effect: '?',
      icon: 'exclamation-circle',
      class: '',
    },
    {
      difference: [1, 7, -1, -7],
      effect: 'Egészség, egyetértés, sikerek és különleges boldogság',
      icon: 'check-circle',
      class: 'inRange',
    },
    {
      difference: [2, 12],
      effect: 'Halál, kedvezőtlen hatások',
      icon: 'circle-xmark',
      class: 'outRange',
    },
    {
      difference: [3, 11],
      effect: 'Nehézségek és szomorúság',
      icon: 'circle-xmark',
      class: 'outRange',
    },
    {
      difference: [4, 10],
      effect: 'Szegénység és ellenségeskedés',
      icon: 'money-bill-alt',
      class: 'outRange',
    },
    {
      difference: [5, 9],
      effect: 'Boldogtalanság, özvegység',
      icon: 'circle-xmark',
      class: 'outRange',
    },
    {
      difference: [6, 8],
      effect: 'Gyerek elvesztése',
      icon: 'baby',
      class: 'outRange',
    },
    {
      difference: [-2, -12],
      effect: 'Hosszú élet',
      icon: 'check-circle',
      class: 'inRange',
    },
    {
      difference: [-3, -11],
      effect: 'Boldogság',
      icon: 'check-circle',
      class: 'inRange',
    },
    {
      difference: [-4, -10],
      effect: 'Gazdagság és boldogság',
      icon: 'check-circle',
      class: 'inRange',
    },
    {
      difference: [-5, -9],
      effect: 'Élvezet és előrejutás',
      icon: 'check-circle',
      class: 'inRange',
    },
    {
      difference: [-6, -8],
      effect: 'Utódok',
      icon: 'baby',
      class: 'inRange',
    },
  ]

  const rashi = (partnerRashi: number) => {
    if (isMale(store.targetPerson.sex)) {
      return rasiInfo.find(r => r.difference.includes(partnerRashi))
    }
    // TODO is woman
  }

  const getMoonPosition = (moon: string) =>
    Object.keys(store.zodiacs).findIndex(zodiac => zodiac == moon) + 1
</script>

<template>
  <div
    v-if="partner"
    title="A Rasi a házastárs karrierjét és anyagi helyzetét tükrözi"
    :class="rashi(partner.rashi)?.class"
  >
    <h5>Rashi</h5>

    {{ getIcon(store.targetPerson.sex) }} {{ store.targetPerson.moon }}
    {{ getMoonPosition(store.targetPerson.moon) }}

    <br />

    {{ getIcon(partner.sex) }} {{ partner.moon }}
    {{ getMoonPosition(partner.moon) }}

    <span>
      <font-awesome-icon :icon="rashi(partner.rashi)?.icon" />
    </span>
    {{ rashi(partner.rashi)?.effect }}
    <br />
    {{ rashi(partner.rashi)?.difference.toString().replace(',', '/') }}
  </div>
</template>

<style scoped></style>
