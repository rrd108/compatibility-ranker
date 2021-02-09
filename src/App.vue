<template>
  <div>
    <header>
      <h1>Compatibility Ranker</h1>
    </header>

    <main>
      <Login v-if="!token" @token="tokenReceived" />
      <Ranker v-if="token" :token="token" />
    </main>
  </div>
</template>

<script>
import Login from '@/components/Login'
import Ranker from '@/components/Ranker'

export default {
  name: 'App',
  components: {
    Login,
    Ranker
  },
  data() {
    return {
      token: null,
    }
  },
  methods: {
    tokenReceived(data) {
      this.token = data
    }
  }
}
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  font-family: 'Quicksand', sans-serif;
  background-color: #484349;
  color: #fff;
}
header {
  display: flex;
  justify-content: space-evenly;
  padding: 1rem;
  background-color: #f5ee9e;
  color: #000;
}
main {
  padding: 1rem;
}
input {
  padding: 0.3rem;
  margin-left: 1rem;
}
</style>
