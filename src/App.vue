<template>
  <div>
    <header>
      <img src="./assets/logo.png" alt="logo">
      <h1>Compatibility Ranker</h1>
    </header>
    <main>
      <router-view/>
    </main>
  </div>
</template>

<script></script>

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
  justify-content: center;
  padding: 1rem;
  background-color: #f5ee9e;
  color: #000;
}
header img {
  height: 3rem;
}
main {
  padding: 1rem;
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
