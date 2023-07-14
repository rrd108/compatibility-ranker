<script setup lang="ts">
  import { computed, ref } from 'vue'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import PersonMoonData from '@/interfaces/PersonMoonData'
  import axios from 'axios'
  import { Naksatra } from '@/interfaces/Naksatra'
  import Rajjus from '@/interfaces/Rajjus'
  import Vedas from '@/interfaces/Vedas'
  import RankerPersonSelect from '@/components/RankerPersonSelect.vue'
  import RankerHeader from '@/components/RankerHeader.vue'
  import RankerPartner from '@/components/RankerPartner.vue'
  import getIcon from '@/composables/useIcon.ts'
  import isMale from '@/composables/useIsMale.ts'
  import { useStore } from '@/store'

  const store = useStore()

  const rajjus: Rajjus = {
    // TODO ezek a naksatra nevek szerepelnek a chart.json-ban 210517-én
    /* ha mindkettő
          láb - állandó vándorlás - walking
          csípő - szegénység - money-bill-alt
          köldök - gyerek elvesztése - baby
          nyak - feleség halála - female
          fej - férj halála - male
        Exception: If Rasi, Graha Maitra, Tara and Mahendra are present then Rajju need not be considered.
      */
    // TODO Purva Phalguni is missing
    Anuradha: 'csipő',
    Ardra: 'nyak',
    Ashlesha: 'láb',
    Ashwini: 'láb',
    Bharani: 'csípő',
    Chitra: 'fej',
    Dhanishta: 'fej',
    Hasta: 'nyak',
    Jyeshta: 'láb',
    Krithika: 'köldök',
    Magha: 'láb',
    Mrigashirsha: 'fej',
    Moola: 'láb',
    Punarvasu: 'köldök',
    'Purva Bhadrapada': 'köldök',
    Purvashadha: 'csípő',
    Pushya: 'csípő',
    Revati: 'láb',
    Rohini: 'nyak',
    Satabhisha: 'nyak',
    Shravana: 'nyak',
    Swati: 'nyak',
    'Uttara Ashadha': 'köldök',
    'Uttara Bhadrapada': 'csípő',
    Uttaraphalguni: 'köldök',
    Vishaka: 'köldök',
  }

  const people = ref<PersonMoonData[]>([])
  axios
    .get(`${import.meta.env.VITE_APP_API_URL}?names`, {
      headers: { Authorization: `ApiKey ${store.token}` },
    })
    .then(response => (people.value = response.data))
    .catch(error => console.error(error))

  const personChanged = (id: number) => {
    store.targetPerson = people.value.find(
      person => person.id == id
    ) as PersonMoonData
    getAnalysis()
  }

  const possiblePartners = ref<PersonAnalysis[]>([])
  const getAnalysis = () => {
    axios
      .get(
        `${import.meta.env.VITE_APP_API_URL}?analysis=${store.targetPerson.id}`,
        {
          headers: { Authorization: `ApiKey ${store.token}` },
        }
      )
      .then(
        response =>
          (possiblePartners.value = response.data.sort(
            (a: { points: number }, b: { points: number }) =>
              b.points - a.points
          ))
      )
      .catch(error => console.error(error))
  }
</script>

<template>
  <div>
    <RankerPersonSelect :people="people" @personChanged="personChanged" />
    <RankerHeader v-if="store.targetPerson" :loading="store.loading" />

    <section v-for="partner in possiblePartners" :key="partner.id">
      <RankerPartner v-if="partner.id" :partner="partner" />
    </section>
  </div>
</template>

<style scoped>
  section {
    margin: 1rem;
    padding: 1rem;
    text-align: center;
    background-color: #f5ee9e;
    color: #000;
  }
  h5 {
    font-size: 1.1rem;
  }
</style>
