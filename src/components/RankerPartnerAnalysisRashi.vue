<script setup lang="ts">
  import getIcon from '@/composables/useIcon.ts'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import { useStore } from '@/store'
  import { PropType } from 'vue'

  const props = defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })

  const store = useStore()

  const rashi = (partnerRashi: number) => {
    if (partnerRashi == 0) {
      return '='
    }
    let rashiNum = Math.abs(partnerRashi) + 1
    return `${rashiNum}/${14 - rashiNum}`
  }
</script>

<template>
  <div
    v-if="partner"
    :class="[
      { inRange: !partner.rashi || partner.rashi == 6 },
      { nice: partner.rashi < 0 }, // TODO is it like this?
    ]"
    title="A Rasi a házastárs karrierjét és anyagi helyzetét tükrözi"
  >
    <h5>TODO Rashi</h5>

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
  </div>
</template>

<style scoped></style>
