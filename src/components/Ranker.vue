<template>
  <div>
    <div class="row">
      <h3>Name</h3>
      <router-link to="addPerson"
        ><font-awesome-icon icon="user-plus"
      /></router-link>
    </div>
    <select @change="getAnalysis" v-model="personId">
      <option v-for="person in people" :key="person.id" :value="person.id">
        {{ person.name }} ({{ person.birth_date.substring(0, 4) }})
      </option>
    </select>

    <article v-show="targetPerson">
      <h1>{{ targetPerson ? targetPerson.name : "" }}</h1>
      <h2>
        {{
          targetPerson
            ? `${targetPerson.birth_date}, ${targetPerson.birth_time}, ${targetPerson.birth_place}`
            : ""
        }}
      </h2>
      <font-awesome-icon icon="sync" spin v-show="loading" />
      <h3
        class="pointer"
        v-show="!loading"
        @click="moonData(targetPerson)"
        title="Get Moon Data"
      >
        <font-awesome-icon icon="moon" />
        {{ targetPerson ? zodiacs[targetPerson.moon] : "" }}
        {{ targetPerson ? targetPerson.moon : "" }}
      </h3>
      <h3
        class="pointer"
        v-show="!loading"
        @click="moonData(targetPerson)"
        title="Get Moon Data"
      >
        <font-awesome-icon icon="meteor" />
        {{ targetPerson ? targetPerson.naksatra : "" }}
      </h3>
      <div class="info">
        <font-awesome-icon icon="info" />
        <textarea v-model="targetPersonInfo"></textarea>
        <font-awesome-icon icon="save" @click="saveInfo" />
      </div>
    </article>

    <div>
      <section v-for="partner in possiblePartners" :key="partner.id">
        <h2>
          {{ partner.name }}
          <font-awesome-icon
            icon="link"
            @click="
              personId = partner.id
              getAnalysis()
            "
          />
        </h2>
        <h3>
          {{ partner.birth_date }} {{ partner.birth_time }},
          {{ partner.birth_place }}
        </h3>
        <h4>
          <font-awesome-icon icon="moon" /> {{ zodiacs[partner.moon] }}
          {{ partner.moon }}
        </h4>
        <h4><font-awesome-icon icon="meteor" /> {{ partner.naksatra }}</h4>
        <ul>
          <li>
            <span>{{ parseInt((partner.points / maxPoints) * 100) }}%</span> ({{
              partner.points
            }}
            points)
          </li>
          <li>
            <ul id="additional">
              <li :class="{ inRange: !veda(partner.naksatra) }">
                <h5>Veda</h5>
                <span><font-awesome-icon icon="skull-crossbones" /></span>
                {{ getNaksatraName(targetPerson.naksatra) }} <br />
                {{ getNaksatraName(partner.naksatra) }}
              </li>
              <li :class="{ inRange: inMoonRange(partner.stridirgha) }">
                <h5>Stridirgha</h5>
                <span>{{ zodiacs[partner.moon] }}</span>
                {{ partner.moon }}<br />{{ partner.stridirgha }}
              </li>
              <li>
                <h5>Rasi</h5>
              </li>
              <li>
                <h5>Raja</h5>
              </li>
            </ul>
          </li>
          <li>
            <span>{{ partner.age_difference }}</span> év korkülönbség
          </li>
        </ul>
        <Stars
          :points="partner.points"
        /><!-- TODO here conside points + 4 additional -->
      </section>
    </div>
  </div>
</template>

<script>
import axios from "axios"
import Stars from "@/components/Stars"
export default {
  name: "Ranker",
  components: { Stars },
  props: ["token"],
  data() {
    return {
      loading: false,
      maxPoints: 36,
      people: [],
      possiblePartners: [],
      personId: null,
      showAdd: false,
      showEditInfo: false,
      zodiacs: {
        Aries: "♈",
        Taurus: "♉",
        Gemini: "♊",
        Cancer: "♋",
        Leo: "♌",
        Virgo: "♍",
        Libra: "♎",
        Scorpio: "♏",
        Sagittarius: "♐",
        Capricorn: "♑",
        Aquarius: "♒",
        Pisces: "♓",
      },
      vedas: {
        // TODO these names should be the same as in the database coming from the api call
        // and in the compatibility chart
        Asvinni: "Jyeshtha",
        Punarvasu: "Uttara Ashadha",
        "Uttara Phalguni": "Purva Bhadrapada",
        Bharani: "Anuradha",
        Pushya: "Purva Ashadha",
        Hasta: "Shatabisha",
        Krithika: "Vishaka",
        Aslesha: "Moola",
        Rohini: "Svati",
        Magha: "Revati",
        Ardra: "Sravana",
        "Purva Phalguni": "Uttara Bhadrapada",
        Mrigashirsha: "Dhanishta",
        Chitra: "Dhanishta",
      },
    }
  },

  created() {
    axios
      .get(`${process.env.VUE_APP_API_URL}?names`, {
        headers: { Authorization: `ApiKey ${this.token}` },
      })
      .then((response) => (this.people = response.data))
      .catch((error) => console.error(error))
  },

  computed: {
    targetPerson() {
      return this.people.find((person) => person.id == this.personId)
    },
    targetPersonInfo: {
      get() {
        return this.targetPerson ? this.targetPerson.info : ""
      },
      set(value) {
        this.targetPerson.info = value
      },
    },
  },

  methods: {
    getAnalysis() {
      axios
        .get(`${process.env.VUE_APP_API_URL}?analysis=${this.personId}`, {
          headers: { Authorization: `ApiKey ${this.token}` },
        })
        .then(
          (response) =>
            (this.possiblePartners = response.data.sort(
              (a, b) => b.points - a.points
            ))
        )
        .catch((error) => console.error(error))
    },
    getNaksatraName(naksatra) {
      return naksatra.substr(0, naksatra.indexOf(","))
    },
    inMoonRange(points) {
      return points <= 6
    },
    moonData(person) {
      this.loading = true
      axios
        .get(`${process.env.VUE_APP_API_URL}?moonData=${person.id}`, {
          headers: { Authorization: `ApiKey ${this.token}` },
        })
        .then((response) => {
          this.loading = false
          if (
            person.naksatra != response.data.naksatra ||
            person.moon != response.data.moon
          ) {
            person.naksatra = response.data.naksatra
            person.moon = response.data.moon
            axios
              .patch(
                process.env.VUE_APP_API_URL,
                {
                  id: person.id,
                  naksatra: person.naksatra,
                  moon: person.moon,
                },
                { headers: { Authorization: `ApiKey ${this.token}` } }
              )
              .then((response) => console.log(response.data))
          }
        })
        .catch((error) => console.error(error))
    },
    saveInfo() {
      axios
        .patch(
          process.env.VUE_APP_API_URL,
          {
            id: this.targetPerson.id,
            info: this.targetPerson.info,
          },
          { headers: { Authorization: `ApiKey ${this.token}` } }
        )
        .then((response) => console.log(response.data))
        .catch((error) => console.error(error))
    },
    veda(partnerNaksatra) {
      if (this.vedas[this.getNaksatraName(partnerNaksatra)] == this.getNaksatraName(this.targetPerson.naksatra)) {
        return true
      }
      if (this.vedas[this.getNaksatraName(this.targetPerson.naksatra)] == this.getNaksatraName(partnerNaksatra)) {
        return true
      }
      return false
    },
  },
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
#additional li {
  background-color: #d64933;
}
span {
  font-size: 15vw;
  display: block;
}
h5 {
  font-size: 1.1rem;
}
.inRange,
#additional li.inRange {
  background-color: #22b73b;
}
.pointer {
  cursor: pointer;
}

@media screen and (min-width: 40em) {
  article h1 {
    font-size: 10vw;
  }
  li {
    width: 29vw;
    height: 20vw;
  }
  span {
    font-size: 10vw;
  }

  #additional {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    grid-gap: 0.3em;
    margin: 0;
  }
  #additional li {
    width: auto;
    height: auto;
    margin: 0;
  }
  #additional span {
    font-size: 2rem;
  }
}
</style>
