<script setup lang="ts">
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import RankerPartnerAnalysisVeda from './RankerPartnerAnalysisVeda.vue'
  import RankerPartnerAnalysisStridirgha from './RankerPartnerAnalysisStridirgha.vue'
  import RankerPartnerAnalysisRashi from './RankerPartnerAnalysisRashi.vue'
  import RankerPartnerAnalysisRajju from './RankerPartnerAnalysisRajju.vue'
  import { PropType } from 'vue'

  defineProps({
    partner: Object as PropType<PersonAnalysis>,
  })
</script>

<template>
  <div v-if="partner" class="analysis">
    <RankerPartnerAnalysisVeda :partner="partner" />
    <RankerPartnerAnalysisStridirgha :partner="partner" />
    <RankerPartnerAnalysisRashi :partner="partner" />
    <RankerPartnerAnalysisRajju :partner="partner" />
  </div>
</template>

<style scoped></style>
