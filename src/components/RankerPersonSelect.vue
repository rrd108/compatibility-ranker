<script setup lang="ts">
  import { PropType, ref } from 'vue'
  import getIcon from '@/composables/useIcon.ts'
  import PersonMoonData from '@/interfaces/PersonMoonData'

  const props = defineProps({
    people: Array as PropType<PersonMoonData[]>,
  })

  const emit = defineEmits<{
    personChanged: [personId: number]
  }>()
  const personId = ref(0)
</script>

<template>
  <div class="row">
    <h3>Name</h3>

    <router-link to="addPerson">
      <font-awesome-icon icon="user-plus" />
    </router-link>
  </div>

  <select @change="emit('personChanged', personId)" v-model="personId">
    <option v-for="person in props.people" :key="person.id" :value="person.id">
      {{ getIcon(person.sex) }}
      {{ person.name }} ({{ person.birth_date.substring(0, 4) }})
      {{ person.naksatra }}, {{ person.pada }} - {{ person.moon }}
    </option>
  </select>
</template>

<style scoped>
  .row {
    display: flex;
    justify-content: space-between;
    font-size: 2rem;
  }
  .row svg {
    cursor: pointer;
  }

  select {
    padding: 0.3rem 0.5rem;
    width: 100%;
    position: sticky;
    top: 0;
    margin: 1rem 0;
  }
</style>
