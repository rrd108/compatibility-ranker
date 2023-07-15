<script setup lang="ts">
  import { ref } from 'vue'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import PersonMoonData from '@/interfaces/PersonMoonData'
  import axios from 'axios'
  import RankerPersonSelect from '@/components/RankerPersonSelect.vue'
  import RankerHeader from '@/components/RankerHeader.vue'
  import RankerPartner from '@/components/RankerPartner.vue'
  import { useStore } from '@/store'

  const store = useStore()

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
    store.loading = true
    axios
      .get(
        `${import.meta.env.VITE_APP_API_URL}?analysis=${store.targetPerson.id}`,
        {
          headers: { Authorization: `ApiKey ${store.token}` },
        }
      )
      .then(response => {
        possiblePartners.value = response.data.sort(
          (a: { points: number }, b: { points: number }) => b.points - a.points
        )
        store.loading = false
      })
      .catch(error => console.error(error))
  }
</script>

<template>
  <div>
    <RankerPersonSelect :people="people" @personChanged="personChanged" />
    <RankerHeader v-if="store.targetPerson.id" :loading="store.loading" />

    <div class="loader" v-show="store.loading">
      <font-awesome-icon icon="sync" spin />
    </div>

    <section
      v-for="partner in possiblePartners"
      :key="partner.id"
      v-if="!store.loading"
    >
      <RankerPartner
        v-if="partner.id"
        :partner="partner"
        @personChanged="personChanged"
      />
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

  .loader {
    text-align: center;
    font-size: 5rem;
  }
</style>
