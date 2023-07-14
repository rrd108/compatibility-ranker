<script>
  import axios from 'axios'
  import Stars from '@/components/Stars.vue'
  export default {
    name: 'Ranker',
    components: { Stars },
    props: ['token'],
    data() {
      return {
        loading: false,
        maxPoints: 36,
        people: [],
        possiblePartners: [],
        personId: null,
        rajjus: {
          // TODO ezek a naksatra nevek szerepelnek a chart.json-ban 210517-én
          /* ha mindkettő
                  láb - állandó vándorlás - walking
                  csípő - szegénység - money-bill-alt
                  köldök - gyerek elvesztése - baby
                  nyak - feleség halála - female
                  fej - férj halála - male
                Exception: If Rasi, Graha Maitra, Tara and Mahendra are present then Rajju need not be considered.
              */
          // TODO Purva Phalguni is missing
          Anuradha: 'csipő',
          Ardra: 'nyak',
          Ashlesha: 'láb',
          Ashwini: 'láb',
          Bharani: 'csípő',
          Chitra: 'fej',
          Dhanishta: 'fej',
          Hasta: 'nyak',
          Jyeshta: 'láb',
          Krithika: 'köldök',
          Magha: 'láb',
          Mrigashirsha: 'fej',
          Moola: 'láb',
          Punarvasu: 'köldök',
          'Purva Bhadrapada': 'köldök',
          Purvashadha: 'csípő',
          Pushya: 'csípő',
          Revati: 'láb',
          Rohini: 'nyak',
          Satabhisha: 'nyak',
          Shravana: 'nyak',
          Swati: 'nyak',
          'Uttara Ashadha': 'köldök',
          'Uttara Bhadrapada': 'csípő',
          Uttaraphalguni: 'köldök',
          Vishaka: 'köldök',
        },
        rajjuIcons: {
          láb: 'walking',
          csípő: 'money-bill-alt',
          köldök: 'baby',
          nyak: 'female',
          fej: 'male',
        },
        showAdd: false,
        showEditInfo: false,
        vedas: {
          // TODO these names should be the same as in the database coming from the api call
          // and in the compatibility chart
          Ashwini: 'Jyeshta',
          Punarvasu: 'Uttara Ashadha',
          'Uttara Phalguni': 'Purva Bhadrapada',
          Bharani: 'Anuradha',
          Pushya: 'Purva Ashadha',
          Hasta: 'Shatabisha',
          Krithika: 'Vishaka',
          Ashlesha: 'Moola',
          Rohini: 'Swati',
          Magha: 'Revati',
          Ardra: 'Shravana',
          'Purva Phalguni': 'Uttara Bhadrapada',
          Mrigashirsha: 'Dhanishta',
          Chitra: 'Dhanishta',
        },
        zodiacs: {
          Aries: '♈',
          Taurus: '♉',
          Gemini: '♊',
          Cancer: '♋',
          Leo: '♌',
          Virgo: '♍',
          Libra: '♎',
          Scorpio: '♏',
          Sagittarius: '♐',
          Capricorn: '♑',
          Aquarius: '♒',
          Pisces: '♓',
        },
      }
    },

    created() {
      axios
        .get(`${import.meta.env.VITE_APP_API_URL}?names`, {
          headers: { Authorization: `ApiKey ${this.token}` },
        })
        .then(response => (this.people = response.data))
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
        },
      },
    },

    methods: {
      analize(id) {
        this.personId = id
        this.getAnalysis()
      },
      getAnalysis() {
        axios
          .get(
            `${import.meta.env.VITE_APP_API_URL}?analysis=${this.personId}`,
            {
              headers: { Authorization: `ApiKey ${this.token}` },
            }
          )
          .then(
            response =>
              (this.possiblePartners = response.data.sort(
                (a, b) => b.points - a.points
              ))
          )
          .catch(error => console.error(error))
      },
      getNaksatraName(naksatra) {
        return naksatra.substr(0, naksatra.indexOf(','))
      },
      inMoonRange(points) {
        return points <= 6
      },
      isAgeDifferenceBig(ageDifference) {
        // the man should be older maximum 10 years or younger maximum 4 years
        console.log(ageDifference)
        if (
          this.targetPerson.sex == 'férfi' ||
          this.targetPerson.sex == 'ferfi' ||
          this.targetPerson.sex == 'male'
        ) {
          if (ageDifference >= -10 && ageDifference <= 4) {
            return false
          }
        }
        if (
          this.targetPerson.sex == 'nő' ||
          this.targetPerson.sex == 'female'
        ) {
          if (ageDifference >= -4 && ageDifference <= 10) {
            return false
          }
        }
        return true
      },
      moonData(person) {
        this.loading = true
        axios
          .get(`${import.meta.env.VITE_APP_API_URL}?moonData=${person.id}`, {
            headers: { Authorization: `ApiKey ${this.token}` },
          })
          .then(response => {
            this.loading = false
            if (
              person.naksatra != response.data.naksatra ||
              person.moon != response.data.moon
            ) {
              person.naksatra = response.data.naksatra
              person.moon = response.data.moon
              axios
                .patch(
                  import.meta.env.VITE_APP_API_URL,
                  {
                    id: person.id,
                    naksatra: person.naksatra,
                    moon: person.moon,
                  },
                  { headers: { Authorization: `ApiKey ${this.token}` } }
                )
                .then(response => console.log(response.data))
            }
          })
          .catch(error => console.error(error))
      },
      rajju(partnerNaksatra) {
        if (
          this.rajjus[this.getNaksatraName(partnerNaksatra)] ==
          this.rajjus[this.getNaksatraName(this.targetPerson.naksatra)]
        ) {
          return this.rajjus[this.getNaksatraName(partnerNaksatra)]
        }
        return false
      },
      rashi(partnerRashi) {
        if (!partnerRashi) {
          return '='
        }
        let rashiNum = Math.abs(partnerRashi) + 1
        return `${rashiNum}/${14 - rashiNum}`
      },
      saveInfo() {
        axios
          .patch(
            import.meta.env.VITE_APP_API_URL,
            {
              id: this.targetPerson.id,
              info: this.targetPerson.info,
            },
            { headers: { Authorization: `ApiKey ${this.token}` } }
          )
          .then(response => console.log(response.data))
          .catch(error => console.error(error))
      },
      veda(partnerNaksatra) {
        if (
          this.vedas[this.getNaksatraName(partnerNaksatra)] ==
          this.getNaksatraName(this.targetPerson.naksatra)
        ) {
          return true
        }
        if (
          this.vedas[this.getNaksatraName(this.targetPerson.naksatra)] ==
          this.getNaksatraName(partnerNaksatra)
        ) {
          return true
        }
        return false
      },
    },
  }
</script>

<template>
  <div>
    <div class="row">
      <h3>Name</h3>

      <router-link to="addPerson">
        <font-awesome-icon icon="user-plus" />
      </router-link>
    </div>

    <select @change="getAnalysis" v-model="personId">
      <option v-for="person in people" :key="person.id" :value="person.id">
        {{ person.name }} ({{ person.birth_date.substring(0, 4) }})
        {{ person.naksatra }} {{ person.moon }}
      </option>
    </select>

    <article v-show="targetPerson">
      <h1>{{ targetPerson ? targetPerson.name : '' }}</h1>

      <h2>
        {{
          targetPerson
            ? `${targetPerson.birth_date}, ${targetPerson.birth_time},
        ${targetPerson.birth_place}`
            : ''
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
        {{ targetPerson ? zodiacs[targetPerson.moon] : '' }}
        {{ targetPerson ? targetPerson.moon : '' }}
      </h3>

      <h3
        class="pointer"
        v-show="!loading"
        @click="moonData(targetPerson)"
        title="Get Moon Data"
      >
        <font-awesome-icon icon="meteor" />
        {{ targetPerson ? targetPerson.naksatra : '' }}
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
          <font-awesome-icon icon="link" @click="analize(partner.id)" />
        </h2>

        <h3>
          {{ partner.birth_date }} {{ partner.birth_time }},
          {{ partner.birth_place }}
        </h3>

        <h4>
          <font-awesome-icon icon="moon" />
          {{ zodiacs[partner.moon] }} {{ partner.moon }}
        </h4>

        <h4>
          <font-awesome-icon icon="meteor" />
          {{ partner.naksatra }}
        </h4>

        <ul>
          <li>
            <span>{{ parseInt((partner.points / maxPoints) * 100) }}%</span>
            ({{ partner.points }} points)
          </li>

          <li>
            <ul id="additional">
              <li :class="{ inRange: !veda(partner.naksatra) }">
                <h5
                  title="A Veda a házastárs személyiségét és temperamentumát tükrözi"
                >
                  Veda
                </h5>

                <span>
                  <font-awesome-icon
                    :icon="
                      veda(partner.naksatra)
                        ? 'skull-crossbones'
                        : 'check-circle'
                    "
                  />
                </span>
                {{ getNaksatraName(targetPerson.naksatra) }}
                <br />
                {{ getNaksatraName(partner.naksatra) }}
              </li>

              <li :class="{ inRange: inMoonRange(partner.stridirgha) }">
                <h5
                  title="A Stridirgha a házastárs élethosszát és egészségét tükrözi"
                >
                  Stridirgha
                </h5>

                <span>{{ zodiacs[partner.moon] }}</span>
                {{ partner.moon }}
                <br />
                {{ partner.stridirgha }}
              </li>

              <li
                :class="[
                  { inRange: !partner.rashi || partner.rashi == 6 },
                  { nice: partner.rashi < 0 }, // TODO is it like this?
                ]"
              >
                <h5
                  title="A Rasi a házastárs karrierjét és anyagi helyzetét tükrözi"
                >
                  ? Rashi
                </h5>

                <span>
                  <font-awesome-icon
                    :icon="
                      !partner.rashi || partner.rashi == 6
                        ? 'check-circle'
                        : 'exclamation-circle'
                    "
                  />
                </span>
                {{ rashi(partner.rashi) }}
              </li>

              <li :class="{ inRange: !rajju(partner.naksatra) }">
                <h5
                  title="A Rajju a házastárs szerelmi életét és kapcsolatait tükrözi"
                >
                  Rajju
                </h5>

                <span>
                  <font-awesome-icon
                    :icon="
                      rajjuIcons[rajju(partner.naksatra)]
                        ? rajjuIcons[rajju(partner.naksatra)]
                        : 'check-circle'
                    "
                  />
                </span>
                {{ rajju(partner.naksatra) ? rajju(partner.naksatra) : 'OK' }}
              </li>
            </ul>
          </li>

          <li :class="{ outRange: isAgeDifferenceBig(partner.age_difference) }">
            <span>{{ partner.age_difference }}</span>
            év korkülönbség
          </li>
        </ul>

        <Stars :points="partner.points" />

        <!-- TODO here conside points + 4 additional -->
      </section>
    </div>
  </div>
</template>

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
    position: sticky;
    top: 0;
  }
  article h1 {
    font-size: 1.5rem;
  }
  article h2 {
    font-size: 1.25rem;
  }
  article h3 {
    font-size: 1rem;
  }
  .info {
    margin-top: 0.5em;
    padding: 0.5em;
    background-color: #fff;
    color: #000;
    text-align: left;
    display: flex;
    align-content: stretch;
  }
  .info p {
    margin: 0.5em;
  }
  .info textarea {
    margin: 0 0.5em;
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
    margin: 0.5em;
    align-items: center;
  }
  li {
    background-color: #0c7c59;
    padding: 0.5em;
    margin: 0.25em 0;
    color: #fff;
    width: 100%;
    text-align: center;
    font-size: 0.75rem;
  }
  #additional {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    grid-gap: 0.3em;
    margin: 0;
    align-items: normal;
    width: 100%;
  }
  #additional li,
  .outRange {
    background-color: #d64933;
  }
  #additional li.nice {
    background-color: #278735;
  }
  span {
    font-size: 1.5rem;
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
      font-size: 5vw;
    }
    li {
      width: 29vw;
      height: 24vw;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      font-size: 1rem;
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
      align-items: normal;
      width: 100%;
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
