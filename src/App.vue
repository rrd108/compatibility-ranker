<template>
  <div>
    <header>
      <font-awesome-icon icon="heart" />
      <h1>Compatibility Ranker</h1>
    </header>

    <main>
      <Ranker />
    </main>
  </div>
</template>

<script>
import Ranker from '@/components/Ranker'
export default {
  name: 'App',
  components: {
    Ranker
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
  background-color: #8B1E3F;
  color: #fff;
}
header {
  display: flex;
  justify-content: space-evenly;
  padding: 1rem;
  background-color: #3c153b;
  color: #fff;
}
header .svg-inline--fa {
  font-size: 5rem;
  margin: 0 1rem;
}
main {
  padding: 1rem;
}
</style>
