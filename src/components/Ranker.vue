<template>
  <div>
    <h3>Name</h3>
    <select @change="getAnalysis" v-model="personId">
      <option v-for="person in people" :key="person.id" :value="person.id">{{person.name}} ({{person.birth_year}})</option>
    </select>

    <article v-show="targetPerson">
      <h1>{{targetPerson ? targetPerson.name : ''}}</h1>
      <h2>{{targetPerson ? targetPerson.birth_year : ''}}</h2>
      <h3>Naksatra: {{targetPerson ? targetPerson.naksatra : ''}}</h3>
      <h4>Moon: {{targetPerson ? zodiacs[targetPerson.moon] : ''}} {{targetPerson ? targetPerson.moon : ''}}</h4>
      <h5 v-html="personInfo"></h5>
    </article>

    <div>
      <section v-for="partner in possiblePartners" :key="partner.id">
        <h2>{{partner.name}}  <font-awesome-icon icon="link" @click="personId = partner.id;getAnalysis()" /></h2>
        <h3>{{partner.birth_date}}</h3>
        <ul>
          <li><span>{{parseInt(partner.points / maxPoints * 100)}}%</span> ({{partner.points}} points)</li>
          <li :class="{outRange : outMoonRange(partner.stridirgha)}"><span>{{zodiacs[partner.moon]}}</span>{{partner.moon}}<br>Stridirgha: {{partner.stridirgha}}</li>
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
  props: ['token'],
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
    axios.get(`${process.env.VUE_APP_API_URL}?names`,
      {headers: {Authorization: `ApiKey ${this.token}`}})
      .then(response => this.people = response.data)
      .catch(error => console.error(error))
  },

  computed: {
    personInfo() {
      //TODO color date
      return this.targetPerson ? this.targetPerson.info.replace(/(?:\r\n|\r|\n)/g, '<br>') : ''
    },
    targetPerson() {
      return this.people.find(person => person.id == this.personId)
    },
  },

  methods: {
    getAnalysis() {
      axios.get(`${process.env.VUE_APP_API_URL}?analysis=${this.personId}`,
        {headers: {Authorization: `ApiKey ${this.token}`}})
        .then(response => this.possiblePartners = response.data.sort((a, b) => b.points - a.points))
        .catch(error => console.error(error))

    },
    outMoonRange(points) {
      return points > 6
    }
  }
}
</script>

<style scoped>
select {
  padding: 0.3rem 0.5rem;
  width: 100%;
  position: sticky;
  top: 0;
}
article {
  background-color: #58a4b0;
  margin: 1rem;
  padding: 1rem;
  text-align: center;
}
article h1 {
  font-size: 10vw;
}
h5 {
  background-color: #fff;
  color: #000;
}
section {
  margin: 1rem;
  padding: 1rem;
  text-align: center;
  background-color: #f5ee9e;
  color: #000;
}
ul {
  list-style: none;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  margin: 1rem;
  align-items: center;
}
li {
  background-color: #0c7c59;
  padding: 1rem;
  margin: 1rem 0;
  color: #fff;
  width: 100%;
  text-align: center;
}
span {
  font-size: 15vw;
  display: block;
}
.outRange {
  background-color: #d64933;
}

@media screen and (min-width: 40em) {
  article h1 {
    font-size: 10vw;
  }
  li {
    width: 25vw;
    height: 20vw;
  }
  span {
    font-size: 10vw;
  }
}

</style>
