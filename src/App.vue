<script setup lang="ts">
  import AppHeader from './components/AppHeader.vue'
</script>

<template>
  <div class="app">
    <AppHeader />
    <main>
      <router-view />
    </main>
  </div>
</template>

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
  main {
    padding: 1rem;
  }
  ul {
    list-style: none;
  }
  input {
    padding: 0.3rem;
    margin-left: 1rem;
  }
  button {
    margin: 1rem auto;
    padding: 0.6rem 1rem;
    font-size: 1.5rem;
    width: 90%;
    background-color: #58a4b0;
    color: #fff;
    font-weight: bold;
  }
  a {
    color: #fff;
  }
</style>
