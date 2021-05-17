<template>
  <div>
    <div class="row">
      <h3>Name</h3>
      <router-link to="addPerson"><font-awesome-icon icon="user-plus" /></router-link>
    </div>
    <select @change="getAnalysis" v-model="personId">
      <option v-for="person in people" :key="person.id" :value="person.id">{{person.name}} ({{person.birth_date.substring(0,4)}})</option>
    </select>

    <article v-show="targetPerson">
      <h1>{{targetPerson ? targetPerson.name : ''}}</h1>
      <h2>{{targetPerson ? `${targetPerson.birth_date}, ${targetPerson.birth_time}, ${targetPerson.birth_place}` : ''}}</h2>
      <font-awesome-icon icon="sync" spin v-show="loading" />
      <h3 class="pointer" v-show="!loading" @click="moonData(targetPerson)" title="Get Moon Data">
        <font-awesome-icon icon="moon" /> {{targetPerson ? zodiacs[targetPerson.moon] : ''}} {{targetPerson ? targetPerson.moon : ''}}
      </h3>
      <h3 class="pointer" v-show="!loading" @click="moonData(targetPerson)" title="Get Moon Data">
        <font-awesome-icon icon="meteor" /> {{targetPerson ? targetPerson.naksatra : ''}}
      </h3>
      <div class="info">
        <font-awesome-icon icon="info" />
        <textarea v-model="targetPersonInfo"></textarea>
        <font-awesome-icon icon="save" @click="saveInfo" />
      </div>
    </article>

    <div>
      <section v-for="partner in possiblePartners" :key="partner.id">
        <h2>{{partner.name}}  <font-awesome-icon icon="link" @click="personId = partner.id;getAnalysis()" /></h2>
        <h3>{{partner.birth_date}} {{partner.birth_time}}, {{partner.birth_place}}</h3>
        <h4><font-awesome-icon icon="moon" /> {{zodiacs[partner.moon]}} {{partner.moon}}</h4>
        <h4><font-awesome-icon icon="meteor" /> {{partner.naksatra}}</h4>
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
      loading: false,
      maxPoints: 36,
      people: [],
      possiblePartners: [],
      personId: null,
      showAdd: false,
      showEditInfo: false,
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
    targetPerson() {
      return this.people.find(person => person.id == this.personId)
    },
    targetPersonInfo: {
      get() {
        return this.targetPerson ? this.targetPerson.info : ''
      },
      set(value) {
        this.targetPerson.info = value
      }
    }
  },

  methods: {
    getAnalysis() {
      axios.get(`${process.env.VUE_APP_API_URL}?analysis=${this.personId}`,
        {headers: {Authorization: `ApiKey ${this.token}`}})
        .then(response => this.possiblePartners = response.data.sort((a, b) => b.points - a.points))
        .catch(error => console.error(error))
    },
    moonData(person) {
      this.loading = true
      axios.get(`${process.env.VUE_APP_API_URL}?moonData=${person.id}`,
        {headers: {Authorization: `ApiKey ${this.token}`}})
        .then(response => {
          this.loading = false
          if (person.naksatra != response.data.naksatra || person.moon != response.data.moon) {
            person.naksatra = response.data.naksatra
            person.moon = response.data.moon
            axios.patch(
              process.env.VUE_APP_API_URL,
              {
                id: person.id,
                naksatra: person.naksatra,
                moon: person.moon
              },
              { headers: {Authorization: `ApiKey ${this.token}`} })
              .then(response => console.log(response.data))
            }
          })
        .catch(error => console.error(error))
    },
    outMoonRange(points) {
      return points > 6
    },
    saveInfo() {
      axios.patch(
        process.env.VUE_APP_API_URL,
        {
          id: this.targetPerson.id,
          info: this.targetPerson.info
        },
        { headers: {Authorization: `ApiKey ${this.token}`} })
        .then(response => console.log(response.data))
        .catch(error => console.error(error))
    },
  }
}
</script>

<style scoped>
.row {
  display: flex;
  justify-content: space-between;
  font-size: 2rem;
}
.row svg {
  cursor: pointer;
}
select {
  padding: 0.3rem 0.5rem;
  width: 100%;
  position: sticky;
  top: 0;
  margin: 1rem 0;
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
.info {
  margin-top: 1rem;
  padding: 1rem;
  background-color: #fff;
  color: #000;
  text-align: left;
  display: flex;
  align-content: stretch;
}
.info p {
  margin: 1rem;
}
.info textarea {
  margin: 0 1rem;
  width: 100%;
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
.pointer {
  cursor: pointer;
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
