<script setup lang="ts">
  import getIcon from '@/composables/useIcon.ts'
  import { Naksatra } from '@/interfaces/Naksatra'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import { useStore } from '@/store'
  import { PropType } from 'vue'

  defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })

  const store = useStore()

  const veda = (partnerNaksatra: Naksatra) =>
    store.vedas[partnerNaksatra] == store.targetPerson.naksatra ||
    store.vedas[store.targetPerson.naksatra] == partnerNaksatra
</script>

<template>
  <div
    v-if="partner"
    :class="veda(partner.naksatra) ? 'outRange' : 'inRange'"
    title="A Veda a házastárs személyiségét és temperamentumát tükrözi. (Nincs kivétel)"
  >
    <h5>Veda</h5>

    {{ getIcon(store.targetPerson.sex) }} {{ store.targetPerson.naksatra }}
    <br />
    {{ getIcon(partner.sex) }} {{ partner.naksatra }}

    <span>
      <font-awesome-icon
        :icon="veda(partner.naksatra) ? 'skull-crossbones' : 'check-circle'"
      />
    </span>
  </div>
</template>

<style scoped></style>
