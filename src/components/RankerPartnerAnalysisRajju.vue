<script setup lang="ts">
  import { Naksatra } from '@/interfaces/Naksatra'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import { useStore } from '@/store'
  import { PropType } from 'vue'

  const props = defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })

  const store = useStore()

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
  <div v-if="partner" :class="rajju(partner.naksatra) ? 'outRange' : 'inRange'">
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
  </div>
</template>

<style scoped></style>
