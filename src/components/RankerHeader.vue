<script setup lang="ts">
  import PersonMoonData from '@/interfaces/PersonMoonData'
  import getIcon from '@/composables/useIcon.ts'

  const props = defineProps<{
    loading: boolean
    targetPerson: PersonMoonData
  }>()
</script>

<template>
  <header>
    <h1>{{ getIcon(targetPerson.sex) }} {{ targetPerson.name }}</h1>

    <h2>
      {{
        targetPerson
          ? `${targetPerson.birth_date}, ${targetPerson.birth_time}, ${targetPerson.birth_place}`
          : ''
      }}
    </h2>

    <font-awesome-icon icon="sync" spin v-show="loading" />

    <h3
      class="pointer"
      v-show="!loading"
      @click="moonData(targetPerson)"
      title="Get Moon Data"
    >
      <font-awesome-icon icon="moon" />
      {{ targetPerson ? zodiacs[targetPerson.moon] : '' }}
      {{ targetPerson ? targetPerson.moon : '' }}
    </h3>

    <h3
      class="pointer"
      v-show="!loading"
      @click="moonData(targetPerson)"
      title="Get Moon Data"
    >
      <font-awesome-icon icon="meteor" />
      {{ targetPerson.naksatra }}, {{ targetPerson.pada }}
    </h3>

    <div class="info">
      <font-awesome-icon icon="info" />

      <textarea>TODO {{ targetPerson?.info }}</textarea>

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
    font-size: 1.5rem;
  }
  h2 {
    font-size: 1.25rem;
  }
  h3 {
    font-size: 1rem;
  }
</style>
