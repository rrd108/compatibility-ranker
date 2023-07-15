<script setup lang="ts">
  import getIcon from '@/composables/useIcon.ts'
  import { Naksatra } from '@/interfaces/Naksatra'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import { useStore } from '@/store'
  import { PropType, computed } from 'vue'

  const props = defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })

  const store = useStore()

  const rajju = (partnerNaksatra: Naksatra) =>
    store.rajjus[partnerNaksatra] == store.rajjus[store.targetPerson.naksatra]
      ? store.rajjus[partnerNaksatra]
      : false

  const getRajjuInfo = computed(() =>
    store.rajjuInfo.find(
      rajju => rajju.area == store.rajjus[props.partner.naksatra]
    )
  )
</script>

<template>
  <div
    v-if="partner"
    :class="rajju(partner.naksatra) ? 'outRange' : 'inRange'"
    title="A Rajju azt jelzi, hogy honnan kell számítani váratlan problémákra"
  >
    <h5>Rajju</h5>

    {{ getIcon(store.targetPerson.sex) }}
    {{ store.targetPerson.naksatra }}
    ({{ store.rajjus[store.targetPerson.naksatra] }})
    <br />
    {{ getIcon(partner.sex) }} {{ partner.naksatra }} ({{
      store.rajjus[partner.naksatra]
    }})

    <span>
      <font-awesome-icon
        :icon="
          store.rajjus[store.targetPerson.naksatra] !=
          store.rajjus[partner.naksatra]
            ? 'check-circle'
            : getRajjuInfo?.icon
        "
      />
    </span>
    {{ rajju(partner.naksatra) ? getRajjuInfo?.effect : 'OK' }}
  </div>
</template>

<style scoped></style>
