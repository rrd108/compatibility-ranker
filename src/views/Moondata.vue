<template>
  <div v-if="token">
    <h1>Naksatra</h1>
    <ul>
      <li v-for="person in people" :key="person.id">
        <h2>
          <font-awesome-icon :icon="(person.sex == 'férfi' || person.sex == 'male') ? 'male' : 'female'" />
          {{person.name}}
        </h2>
        <p>
          {{person.birth_date}}
          {{person.birth_time}}
          {{person.birth_place}}
        </p>
        <span @click="moonData(person)">
          <font-awesome-icon icon="moon" /> {{person.moon}}
          <font-awesome-icon icon="meteor" /> {{person.naksatra}}
        </span>
      </li>
    </ul>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Naksatra',
  data() {
    return {
      people: [],
      token: window.sessionStorage.getItem('cR')
    }
  },
  created() {
    axios.get(`${process.env.VUE_APP_API_URL}?names`,
      {headers: {Authorization: `ApiKey ${this.token}`}})
      .then(response => this.people = response.data)
      .catch(error => console.error(error))
  },
  methods: {
    moonData(person) {
      axios.get(`${process.env.VUE_APP_API_URL}?moonData=${person.id}`,
        {headers: {Authorization: `ApiKey ${this.token}`}})
        .then(response => {
          if (person.naksatra != response.data.naksatra) {
            person.naksatra = response.data.naksatra
            axios.patch(
              process.env.VUE_APP_API_URL,
              {
                id: person.id,
                naksatra: person.naksatra
              },
              { headers: {Authorization: `ApiKey ${this.token}`} })
              .then(response => console.log(response.data))
            }
          })
        .catch(error => console.error(error))
    }
  }
}
</script>

<style scoped>
ul {
  margin: 1rem;
  list-style: none;
}
li {
  margin-bottom: 1rem;
}
</style>