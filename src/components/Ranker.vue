<template>
  <div>
    <h3>Name</h3>
    <select @change="getAnalysis" v-model="personId">
      <option v-for="person in people" :key="person.id" :value="person.id">{{person.name}}</option>
    </select>

    <article v-show="targetPerson">
      <h1>{{targetPerson ? targetPerson.name : ''}}</h1>
      <h2>{{targetPerson ? targetPerson.birth_year : ''}}</h2>
      <h3>Naksatra: {{targetPerson ? targetPerson.naksatra : ''}}</h3>
      <h4>Moon: {{targetPerson ? zodiacs[targetPerson.moon] : ''}} {{targetPerson ? targetPerson.moon : ''}}</h4>
    </article>

    <div>
      <section v-for="partner in possiblePartners" :key="partner.id">
        <h2>{{partner.name}}</h2>
        <h3>{{partner.birth_date}}</h3>
        <ul>
          <li><span>{{parseInt(partner.points / maxPoints * 100)}}%</span> ({{partner.points}} points)</li>
          <li :class="{inRange : inMoonRange(partner.moonPosition)}"><span>{{zodiacs[partner.moon]}}</span> Moon: {{partner.moonPosition}} {{partner.moon}}</li>
          <li><span>{{partner.age_difference}}</span> év korkülönbség</li>
        </ul>
        <Stars :points="partner.points" />
      </section>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Stars from '@/components/Stars'
export default {
  name: 'Ranker',
  components: {Stars},
  data() {
    return {
      maxPoints: 36,
      people: [],
      possiblePartners: [],
      personId: null,
      zodiacs: {'Aries': '♈', 'Taurus': '♉', 'Gemini' : '♊', 'Cancer' : '♋'	, 'Leo' : '♌', 'Virgo' : '♍', 'Libra' : '♎', 'Scorpio': '♏', 'Sagittarius' : '♐', 'Capricorn': '♑', 'Aquarius': '♒', 'Pisces' : '♓'}
    }
  },

  created() {
    axios.get(`${process.env.VUE_APP_API_URL}?names`)
      .then(response => this.people = response.data)
      .catch(error => console.error(error))
  },

  computed: {
    targetPerson() {
      return this.people.find(person => person.id == this.personId)
    },
  },

  methods: {
    getAnalysis() {
      axios.get(`${process.env.VUE_APP_API_URL}?analysis=${this.personId}`)
        .then(response => this.possiblePartners = response.data.sort((a, b) => b.points - a.points))
        .catch(error => console.error(error))

    },
    inMoonRange(points) {
      return ((-11 <= points && points <= -6) || (1 <= points && points <= 6)) ? true : false
    }
  }
}
</script>

<style scoped>
article {
  background-color: #89bd9e;
  margin: 1rem;
  padding: 1rem;
  text-align: center;
}
section {
  margin: 1rem;
  padding: 1rem;
  text-align: center;
  background-color: #db4c40;
}
ul {
  list-style: none;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  margin: 1rem;
}
li {
  background-color: #f0c987;
  color: #000;
  padding: 1rem;
  margin: 1rem 0;
}
span {
  font-size: 4rem;
  display: block;
  width: 20vw;
}
.inRange {
  background-color: #89bd9e;
  color: #fff;
}
</style>
