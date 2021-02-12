<template>
  <div>
    <form @submit.prevent="login">
      <label for="user">
        <font-awesome-icon icon="user-astronaut" />
        User
      </label>
      <input type="text" name="user" v-model="user">

      <label for="password">
        <font-awesome-icon icon="key" />
        Pass
      </label>
      <input type="password" name="password" v-model="password">

      <button type="submit">Login</button>

      <aside v-show="error">
        <font-awesome-icon icon="bomb" />
        {{error}}
      </aside>
    </form>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Login',
  data() {
    return {
      error: null,
      user: null,
      password: null,
    }
  },
  methods: {
    login() {
      this.error = null
      axios.post(`${process.env.VUE_APP_API_URL}?get-token`, {user: this.user, password: this.password})
        .then(response => {
          if (response.data.error) {
            this.error = response.data.error
            return
          }
          this.$emit('token', response.data)
        })
        .catch(error => this.error = error)
    }
  },
}
</script>

<style scoped>
div {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 75vh;
}
form {
  display: flex;
  flex-wrap: wrap;
  width: 100vw;
}
label {
  width: 30%;
  margin-bottom: 1rem;
  text-align: right;
  font-size: 1.5rem;
}
input {
  width: 60%;
  font-size: 1.5rem;
  margin-bottom: 0.3rem;
}
aside {
  margin: 1rem auto;
  padding: 0.6rem 1rem;
  font-size: 1rem;
  width: 90vw;
  background-color: #d64933;
  font-weight: bold;
}
</style>