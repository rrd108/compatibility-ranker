<script setup lang="ts">
  import { Naksatra } from '@/interfaces/Naksatra'
  import PersonMoonData from '@/interfaces/PersonMoonData'
  import { useStore } from '@/store'
  import { PropType } from 'vue'
  import getIcon from '@/composables/useIcon.ts'
  import isMale from '@/composables/useIsMale'

  const props = defineProps({
    partner: Object as PropType<PersonMoonData>,
  })

  const store = useStore()

  const veda = (partnerNaksatra: Naksatra) =>
    store.vedas[partnerNaksatra] == store.targetPerson.naksatra ||
    store.vedas[store.targetPerson.naksatra] == partnerNaksatra

  // TODO The Nakshatra of the man should be at least 14 away from the woman's
  // at server side we calculate the zodiac difference at we want smaller then 6? where this number came?
  // is it good like that? check in Kala
  const inMoonRange = (difference: number) => difference <= 6

  const naksatraDistanceForStridirgha = 14
  const naksatraDistance = (partnerNaksatra: Naksatra) => {
    const manNaksatraPosition = store.naksatras.findIndex(naksatra =>
      isMale(store.targetPerson.sex)
        ? naksatra == store.targetPerson.naksatra
        : naksatra == partnerNaksatra
    )
    const womanNaksatraPosition = store.naksatras.findIndex(naksatra =>
      isMale(store.targetPerson.sex)
        ? naksatra == partnerNaksatra
        : naksatra == store.targetPerson.naksatra
    )
    if (manNaksatraPosition == -1 || womanNaksatraPosition == -1) return -1

    return womanNaksatraPosition >= manNaksatraPosition
      ? womanNaksatraPosition - manNaksatraPosition
      : store.naksatras.length - manNaksatraPosition + womanNaksatraPosition
  }

  const rashi = (partnerRashi: number) => {
    if (partnerRashi == 0) {
      return '='
    }
    let rashiNum = Math.abs(partnerRashi) + 1
    return `${rashiNum}/${14 - rashiNum}`
  }

  const rajju = (partnerNaksatra: Naksatra) => {
    if (
      store.rajjus[partnerNaksatra] == store.rajjus[store.targetPerson.naksatra]
    ) {
      return store.rajjus[partnerNaksatra]
    }
    return false
  }
</script>

<template>
  <ul>
    <li :class="{ inRange: !veda(partner.naksatra) }">
      <h5
        title="A Veda a házastárs személyiségét és temperamentumát tükrözi. (Nincs kivétel)"
      >
        Veda
      </h5>
      <span>
        <font-awesome-icon
          :icon="veda(partner.naksatra) ? 'skull-crossbones' : 'check-circle'"
        />
      </span>
      {{ getIcon(store.targetPerson.sex) }} {{ store.targetPerson.naksatra }}
      <br />
      {{ getIcon(partner.sex) }} {{ partner.naksatra }}
    </li>

    <li :class="{ inRange: inMoonRange(partner.stridirgha) }">
      <h5
        title="A Stridirgha azt mutatja, hogy a férfi energia mennyire tud áramolni a nő felé."
      >
        Stridirgha
      </h5>

      <span title="Partner holdja">{{ store.zodiacs[partner.moon] }}</span>
      {{ partner.moon }}
      <br />
      <span title="Hold helyzetek különbsége a zodiákus jegyek alapján">
        {{ partner.stridirgha }}</span
      >
    </li>

    <li
      :class="{
        inRange:
          naksatraDistance(partner.naksatra) >= naksatraDistanceForStridirgha,
      }"
    >
      <h5
        title="A Stridirgha azt mutatja, hogy a férfi energia mennyire tud áramolni a nő felé."
      >
        Stridirgha
      </h5>

      {{ getIcon(store.targetPerson.sex) }}
      {{ store.targetPerson.naksatra }}
      <br />
      {{ getIcon(partner.sex) }} {{ partner.naksatra }}

      <span
        :title="`Az értéknek legalább ${naksatraDistanceForStridirgha}-nek kell lennie, minél nagyobb annál jobb`"
      >
        {{ naksatraDistance(partner.naksatra) }}
      </span>
    </li>

    <li
      :class="[
        { inRange: !partner.rashi || partner.rashi == 6 },
        { nice: partner.rashi < 0 }, // TODO is it like this?
      ]"
    >
      <h5 title="A Rasi a házastárs karrierjét és anyagi helyzetét tükrözi">
        TODO Rashi
      </h5>

      {{ getIcon(store.targetPerson.sex) }}
      {{ store.targetPerson.moon }}
      <br />
      {{ getIcon(partner.sex) }} {{ partner.moon }}
      <span>
        <font-awesome-icon
          :icon="
            !partner.rashi || partner.rashi == 6
              ? 'check-circle'
              : 'exclamation-circle'
          "
        />
      </span>
      {{ rashi(partner.rashi) }}
    </li>

    <li :class="{ inRange: !rajju(partner.naksatra) }">
      <h5 title="A Rajju a házastárs szerelmi életét és kapcsolatait tükrözi">
        Rajju
      </h5>

      <span>
        <font-awesome-icon
          :icon="
            store.rajjuIcons[rajju(partner.naksatra)]
              ? store.rajjuIcons[rajju(partner.naksatra)]
              : 'check-circle'
          "
        />
      </span>
      {{ rajju(partner.naksatra) ? rajju(partner.naksatra) : 'OK' }}
    </li>
  </ul>
</template>

<style scoped>
  li,
  .outRange {
    background-color: #d64933;
  }
  .nice {
    background-color: #278735;
  }
  .inRange {
    background-color: #22b73b;
  }
  .pointer {
    cursor: pointer;
  }
  span {
    font-size: 1.5rem;
    display: block;
  }
  h5 {
    font-size: 1.1rem;
  }
</style>
