<script setup lang="ts">
  import { ref } from 'vue'
  import axios from 'axios'
  import { useStore } from '@/store'

  const error = ref()
  const user = ref()
  const password = ref()
  const store = useStore()

  const login = () => {
    error.value = null
    axios
      .post(`${import.meta.env.VITE_APP_API_URL}?get-token`, {
        user: user.value,
        password: password.value,
      })
      .then(response => {
        if (response.data.error) {
          error.value = response.data.error
          return
        }
        store.token = response.data
      })
      .catch(error => (error.value = error))
  }
</script>

<template>
  <div>
    <form @submit.prevent="login">
      <label for="user">
        <font-awesome-icon icon="user-astronaut" />
        User
      </label>
      <input type="text" name="user" v-model="user" />
      <label for="password">
        <font-awesome-icon icon="key" />
        Pass
      </label>
      <input type="password" name="password" v-model="password" />
      <button type="submit">Login</button>
      <aside v-show="error">
        <font-awesome-icon icon="bomb" />
        {{ error }}
      </aside>
    </form>
  </div>
</template>

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
