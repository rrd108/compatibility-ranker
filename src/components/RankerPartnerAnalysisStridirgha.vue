<script setup lang="ts">
  import getIcon from '@/composables/useIcon.ts'
  import isMale from '@/composables/useIsMale'
  import { Naksatra } from '@/interfaces/Naksatra'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import { useStore } from '@/store'
  import { PropType, computed } from 'vue'

  const props = defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })

  const store = useStore()

  // TODO move back to server side #9
  const naksatraDistanceForStridirgha = 14
  const manNaksatraPosition = (partnerNaksatra: Naksatra) =>
    store.naksatras.findIndex(naksatra =>
      isMale(store.targetPerson.sex)
        ? naksatra == store.targetPerson.naksatra
        : naksatra == partnerNaksatra
    )

  const womanNaksatraPosition = (partnerNaksatra: Naksatra) =>
    store.naksatras.findIndex(naksatra =>
      isMale(store.targetPerson.sex)
        ? naksatra == partnerNaksatra
        : naksatra == store.targetPerson.naksatra
    )

  const naksatraDistance = (partnerNaksatra: Naksatra) => {
    const manNaksatraPos = manNaksatraPosition(partnerNaksatra)
    const womanNaksatraPos = womanNaksatraPosition(partnerNaksatra)
    if (manNaksatraPos == -1 || womanNaksatraPos == -1) return -1

    return womanNaksatraPos >= manNaksatraPos
      ? store.naksatras.length - (womanNaksatraPos - manNaksatraPos)
      : manNaksatraPos - womanNaksatraPos
  }

  const acceptable = computed(
    () =>
      naksatraDistance(props.partner!.naksatra) >= naksatraDistanceForStridirgha
  )
</script>

<template>
  <div
    v-if="partner"
    :class="acceptable ? 'inRange' : 'outRange'"
    title="A Stridirgha azt mutatja, hogy a férfi energia mennyire tud áramolni a nő felé."
  >
    <h5>Stridirgha</h5>

    {{ getIcon(store.targetPerson.sex) }}
    {{ store.targetPerson.naksatra }}
    {{
      isMale(store.targetPerson.sex)
        ? manNaksatraPosition(store.targetPerson.naksatra)
        : womanNaksatraPosition(store.targetPerson.naksatra)
    }}
    <br />
    {{ getIcon(partner.sex) }} {{ partner.naksatra }}
    {{
      isMale(store.targetPerson.sex)
        ? womanNaksatraPosition(partner.naksatra)
        : manNaksatraPosition(partner.naksatra)
    }}

    <span
      :title="`Az értéknek legalább ${naksatraDistanceForStridirgha}-nek kell lennie, minél nagyobb annál jobb`"
    >
      <font-awesome-icon
        :icon="acceptable ? 'check-circle' : 'skull-crossbones'"
      />
    </span>
    {{ naksatraDistance(partner.naksatra) }}
  </div>
</template>

<style scoped></style>
