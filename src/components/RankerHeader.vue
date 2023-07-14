<script setup lang="ts">
  import getIcon from '@/composables/useIcon.ts'
  import moonData from '@/composables/useMoonData'
  import { useStore } from '@/store'
  import axios from 'axios'

  const store = useStore()

  const saveInfo = () => {
    axios
      .patch(
        import.meta.env.VITE_APP_API_URL,
        {
          id: store.targetPerson.id,
          info: store.targetPerson.info,
        },
        { headers: { Authorization: `ApiKey ${store.token}` } }
      )
      .then(response => console.log(response.data))
      .catch(error => console.error(error))
  }
</script>

<template>
  <header>
    <h1>{{ getIcon(store.targetPerson.sex) }} {{ store.targetPerson.name }}</h1>

    <h2>
      {{ store.targetPerson.birth_date }}, {{ store.targetPerson.birth_time }},
      {{ store.targetPerson.birth_place }}
    </h2>

    <h3
      class="pointer"
      v-show="!store.loading"
      @click="moonData(store.targetPerson)"
      title="Get Moon Data"
    >
      <font-awesome-icon icon="moon" />
      {{ store.targetPerson ? store.zodiacs[store.targetPerson.moon] : '' }}
      {{ store.targetPerson ? store.targetPerson.moon : '' }}
    </h3>

    <h3
      class="pointer"
      v-show="!store.loading"
      @click="moonData(store.targetPerson)"
      title="Get Moon Data"
    >
      <font-awesome-icon icon="meteor" />
      {{ store.targetPerson.naksatra }}, {{ store.targetPerson.pada }}
    </h3>

    <div class="info">
      <font-awesome-icon icon="info" />

      <textarea v-model="store.targetPerson.info"></textarea>

      <font-awesome-icon icon="save" @click="saveInfo" />
    </div>
  </header>
</template>

<style scoped>
  header {
    background-color: #58a4b0;
    margin: 1rem;
    padding: 1rem;
    text-align: center;
    position: sticky;
    top: 0;
  }
  h1 {
    font-size: 5rem;
  }
  h2 {
    font-size: 2rem;
  }
  h3 {
    font-size: 1rem;
  }
  div {
    margin-top: 0.5em;
    padding: 0.5em;
    background-color: #fff;
    color: #000;
    text-align: left;
    display: flex;
    align-content: stretch;
  }
  p {
    margin: 0.5em;
  }
  textarea {
    margin: 0 0.5em;
    width: 100%;
  }
</style>
