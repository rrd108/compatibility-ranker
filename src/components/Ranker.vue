<script setup lang="ts">
  import { computed, ref, onMounted } from 'vue'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import PersonMoonData from '@/interfaces/PersonMoonData'
  import axios from 'axios'
  import { Naksatra } from '@/interfaces/Naksatra'
  import Rajjus from '@/interfaces/Rajjus'
  import Vedas from '@/interfaces/Vedas'
  import Zodiacs from '@/interfaces/Zodiacs'
  import { Sex } from '@/interfaces/Person'

  const props = defineProps<{
    token: string
  }>()

  const maxPoints = 35
  const naksatras = [
    'Ashwini',
    'Bharani',
    'Krithika',
    'Rohini',
    'Mrigashirsha',
    'Ardra',
    'Punarvasu',
    'Pushya',
    'Ashlesha',
    'Magha',
    'Purva Phalguni',
    'Uttara Phalguni',
    'Hasta',
    'Chitra',
    'Swati',
    'Vishaka',
    'Anuradha',
    'Jyeshta',
    'Moola',
    'Purva Ashadha',
    'Uttara Ashadha',
    'Shravana',
    'Dhanishta',
    'Shatabhisha',
    'Purva Bhadrapada',
    'Uttara Bhadrapada',
    'Revati',
  ]
  const rajjus: Rajjus = {
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
  }
  const rajjuIcons = {
    láb: 'walking',
    csípő: 'money-bill-alt',
    köldök: 'baby',
    nyak: 'female',
    fej: 'male',
  }
  const vedas: Vedas = {
    // these naksatras must not be married together
    // TODO these names should be the same as in the database coming from the api call and in the compatibility chart
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
  }
  const zodiacs: Zodiacs = {
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
  }

  const loading = ref(false)
  const people = ref<PersonMoonData[]>([])
  const personId = ref(0)

  onMounted(() =>
    axios
      .get(`${import.meta.env.VITE_APP_API_URL}?names`, {
        headers: { Authorization: `ApiKey ${props.token}` },
      })
      .then(response => (people.value = response.data))
      .catch(error => console.error(error))
  )

  const getIcon = (sex: Sex) => (isMale(sex) ? '🧔' : '👰')

  const possiblePartners = ref<PersonAnalysis[]>([])
  const getAnalysis = () => {
    axios
      .get(`${import.meta.env.VITE_APP_API_URL}?analysis=${personId.value}`, {
        headers: { Authorization: `ApiKey ${props.token}` },
      })
      .then(
        response =>
          (possiblePartners.value = response.data.sort(
            (a: { points: number }, b: { points: number }) =>
              b.points - a.points
          ))
      )
      .catch(error => console.error(error))
  }

  const targetPerson = computed(
    () =>
      people.value.find(person => person.id == personId.value) as PersonMoonData
  )

  const moonData = (person: PersonMoonData | undefined) => {
    if (!person) return

    loading.value = true
    axios
      .get(`${import.meta.env.VITE_APP_API_URL}?moonData=${person.id}`, {
        headers: { Authorization: `ApiKey ${props.token}` },
      })
      .then(response => {
        loading.value = false
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
              { headers: { Authorization: `ApiKey ${props.token}` } }
            )
            .then(response => console.log(response.data))
            .catch(error => console.error(error))
        }
      })
      .catch(error => console.error(error))
  }

  const saveInfo = () => {
    axios
      .patch(
        import.meta.env.VITE_APP_API_URL,
        {
          id: targetPerson.value?.id,
          info: targetPerson.value?.info,
        },
        { headers: { Authorization: `ApiKey ${props.token}` } }
      )
      .then(response => console.log(response.data))
      .catch(error => console.error(error))
  }

  const analize = (id: number) => {
    personId.value = id
    getAnalysis()
  }

  const veda = (partnerNaksatra: Naksatra) =>
    vedas[getNaksatraNameForVeda(partnerNaksatra)] ==
      getNaksatraName(targetPerson.value?.naksatra || '') ||
    vedas[getNaksatraNameForVeda(targetPerson.value?.naksatra)] ==
      getNaksatraName(partnerNaksatra)

  const getNaksatraName = (naksatra: string) =>
    naksatra.substring(0, naksatra.indexOf(',')) as Naksatra

  const getNaksatraNameForVeda = (naksatra: string) =>
    naksatra?.substring(0, naksatra.indexOf(',')) as keyof Vedas

  // TODO The Nakshatra of the man should be at least 14 away from the woman's
  // at server side we calculate the zodiac difference at we want smaller then 6? where this number came?
  // is it good like that? check in Kala
  const inMoonRange = (difference: number) => difference <= 6

  const isMale = (sex: Sex) => ['férfi', 'ferfi', 'male'].includes(sex)

  const naksatraDistanceForStridirgha = 14
  const naksatraDistance = (partnerNaksatra: Naksatra) => {
    const manNaksatraPosition = naksatras.findIndex(naksatra =>
      isMale(targetPerson.value.sex)
        ? naksatra == targetPerson.value.naksatra
        : naksatra == partnerNaksatra
    )
    const womanNaksatraPosition = naksatras.findIndex(naksatra =>
      isMale(targetPerson.value.sex)
        ? naksatra == partnerNaksatra
        : naksatra == targetPerson.value.naksatra
    )
    if (manNaksatraPosition == -1 || womanNaksatraPosition == -1) return -1

    return womanNaksatraPosition >= manNaksatraPosition
      ? womanNaksatraPosition - manNaksatraPosition
      : naksatras.length - manNaksatraPosition + womanNaksatraPosition
  }

  const rashi = (partnerRashi: number) => {
    if (!partnerRashi) {
      return '='
    }
    let rashiNum = Math.abs(partnerRashi) + 1
    return `${rashiNum}/${14 - rashiNum}`
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
        {{ getIcon(person.sex) }}
        {{ person.name }} ({{ person.birth_date.substring(0, 4) }})
        {{ person.naksatra }}, {{ person.pada }} - {{ person.moon }}
      </option>
    </select>

    <article v-if="targetPerson">
      <h1>{{ getIcon(targetPerson.sex) }} {{ targetPerson.name }}</h1>

      <h2>
        {{
          targetPerson
            ? `${targetPerson.birth_date}, ${targetPerson.birth_time}, ${targetPerson.birth_place}`
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
        {{ targetPerson.naksatra }}, {{ targetPerson.pada }}
      </h3>

      <div class="info">
        <font-awesome-icon icon="info" />

        <textarea>TODO {{ targetPerson?.info }}</textarea>

        <font-awesome-icon icon="save" @click="saveInfo" />
      </div>
    </article>

    <div>
      <section v-for="partner in possiblePartners" :key="partner.id">
        <h2>
          {{ getIcon(partner.sex) }} {{ partner.name }}
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
          {{ partner.naksatra }}, {{ partner.pada }}
          <small class="outRange" v-if="!naksatras.includes(partner.naksatra)"
            >Unknown Naksatra</small
          >
        </h4>

        <ul>
          <li>
            <span>{{ Math.floor((partner.points / maxPoints) * 100) }}%</span>
            ({{ partner.points }} points)
          </li>
          <li>
            <ul id="additional">
              <li :class="{ inRange: !veda(partner.naksatra) }">
                <h5
                  title="A Veda a házastárs személyiségét és temperamentumát tükrözi. (Nincs kivétel)"
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
                {{ targetPerson.naksatra }}
                <br />
                {{ partner.naksatra }}
              </li>

              <li :class="{ inRange: inMoonRange(partner.stridirgha) }">
                <h5
                  title="A Stridirgha azt mutatja, hogy a férfi energia mennyire tud áramolni a nő felé."
                >
                  Stridirgha
                </h5>

                <span title="Partner holdja">{{ zodiacs[partner.moon] }}</span>
                {{ partner.moon }}
                <br />
                <span
                  title="Hold helyzetek különbsége a zodiákus jegyek alapján"
                >
                  {{ partner.stridirgha }}</span
                >
              </li>

              <li
                :class="{
                  inRange:
                    naksatraDistance(partner.naksatra) >=
                    naksatraDistanceForStridirgha,
                }"
              >
                <h5
                  title="A Stridirgha azt mutatja, hogy a férfi energia mennyire tud áramolni a nő felé."
                >
                  Stridirgha
                </h5>

                <small v-if="isMale(targetPerson.sex)">
                  {{ targetPerson.naksatra }}
                  <br />
                  {{ partner.naksatra }}
                </small>
                <small v-if="!isMale(targetPerson.sex)">
                  {{ partner.naksatra }}
                  <br />
                  {{ targetPerson.naksatra }}
                </small>

                <span
                  :title="`Az értéknek legalább ${naksatraDistanceForStridirgha}-nek kell lennie, minél nagyobb annál jobb`"
                >
                  {{ naksatraDistance(partner.naksatra) }}
                </span>
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

                {{ targetPerson.moon }}
                <br />
                {{ partner.moon }}
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
            </ul>
          </li>
        </ul>
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
