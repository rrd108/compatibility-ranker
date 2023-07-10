<template>

  <article>

    <h2>Add new</h2>

    <form @submit.prevent="addPerson">

      <fieldset>

        <label>

          <font-awesome-icon icon="user-astronaut" />
           Name
        </label>

        <input type="text" v-model="name" />

      </fieldset>

      <fieldset>

        <label>

          <font-awesome-icon icon="baby" />
           Date
        </label>

        <input type="text" v-model="birth_date" />

      </fieldset>

      <fieldset>

        <label>

          <font-awesome-icon icon="clock" />
           Time
        </label>

        <input type="text" v-model="birth_time" />

      </fieldset>

      <fieldset>

        <label>

          <font-awesome-icon icon="globe" />
           Place
        </label>

        <input type="text" v-model="birth_place" @blur="getMoonData" />

      </fieldset>

      <fieldset>

        <label>

          <font-awesome-icon icon="mars-stroke" />
           Sex
        </label>

        <input type="text" v-model="sex" />

      </fieldset>

      <fieldset>

        <label>

          <font-awesome-icon icon="moon" />
           Moon
        </label>

        <input type="text" v-model="moon" />

      </fieldset>

      <fieldset>

        <label>

          <font-awesome-icon icon="meteor" />
           Naksatra
        </label>

        <input type="text" v-model="naksatra" />

      </fieldset>

      <button type="submit">Save</button>

    </form>

  </article>

</template>

<script>
  import axios from 'axios'

  export default {
    name: 'addPerson',
    data() {
      return {
        name: '',
        birth_date: '',
        birth_time: '',
        birth_place: '',
        sex: '',
        moon: '',
        naksatra: '',
        token: window.sessionStorage.getItem('cR'),
      }
    },
    methods: {
      addPerson() {
        axios
          .post(
            `${import.meta.env.VITE_APP_API_URL}?newPerson`,
            {
              data: {
                name: this.name,
                birth_date: this.birth_date,
                birth_time: this.birth_time,
                birth_place: this.birth_place,
                sex: this.sex,
                moon: this.moon,
                naksatra: this.naksatra,
              },
            },
            { headers: { Authorization: `ApiKey ${this.token}` } }
          )
          .then(response => {
            this.id = response.data
            this.name = this.birth_date = this.birth_time = this.birth_place = this.sex = this.moon = this.naksatra =
              ''
          })
          .catch(error => console.error(error))
      },
      getMoonData() {
        axios
          .get(
            `${import.meta.env.VITE_APP_API_URL}?moonData&date=${
              this.birth_date
            }&time=${this.birth_time}&place=${this.birth_place}`,
            { headers: { Authorization: `ApiKey ${this.token}` } }
          )
          .then(response => {
            this.moon = response.data.moon
            this.naksatra = response.data.naksatra
          })
          .catch(error => console.error(error))
      },
    },
  }
</script>

<style scoped>
  form {
    padding-top: 1rem;
  }
  fieldset {
    display: flex;
    border: none;
    font-size: 0.9rem;
  }
  fieldset label {
    text-align: left;
    width: 50%;
  }
  button {
    background: #fff;
    color: #58a4b0;
  }
</style>

