<script setup lang="ts">
  import { computed, ref } from 'vue'
  import PersonAnalysis from '@/interfaces/PersonAnalysis'
  import PersonMoonData from '@/interfaces/PersonMoonData'
  import axios from 'axios'
  import { Naksatra } from '@/interfaces/Naksatra'
  import Rajjus from '@/interfaces/Rajjus'
  import Vedas from '@/interfaces/Vedas'
  import RankerPersonSelect from '@/components/RankerPersonSelect.vue'
  import RankerHeader from '@/components/RankerHeader.vue'
  import getIcon from '@/composables/useIcon.ts'
  import isMale from '@/composables/useIsMale.ts'
  import { useStore } from '@/store'

  const store = useStore()

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

  const loading = ref(false)

  const personId = ref(0)
  const people = ref<PersonMoonData[]>([])
  axios
    .get(`${import.meta.env.VITE_APP_API_URL}?names`, {
      headers: { Authorization: `ApiKey ${store.token}` },
    })
    .then(response => (people.value = response.data))
    .catch(error => console.error(error))

  const personChanged = (id: number) => {
    personId.value = id
    getAnalysis()
  }

  const possiblePartners = ref<PersonAnalysis[]>([])
  const getAnalysis = () => {
    axios
      .get(`${import.meta.env.VITE_APP_API_URL}?analysis=${personId.value}`, {
        headers: { Authorization: `ApiKey ${store.token}` },
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
    if (partnerRashi == 0) {
      return '='
    }
    let rashiNum = Math.abs(partnerRashi) + 1
    return `${rashiNum}/${14 - rashiNum}`
  }

  const rajju = (partnerNaksatra: Naksatra) => {
    if (rajjus[partnerNaksatra] == rajjus[targetPerson.value.naksatra]) {
      return rajjus[partnerNaksatra]
    }
    return false
  }
</script>

<template>
  <div>
    <RankerPersonSelect :people="people" @personChanged="personChanged" />
    <RankerHeader
      v-if="targetPerson"
      :targetPerson="targetPerson"
      :loading="loading"
    />

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
          {{ store.zodiacs[partner.moon] }} {{ partner.moon }}
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
                {{ getIcon(targetPerson.sex) }} {{ targetPerson.naksatra }}
                <br />
                {{ getIcon(partner.sex) }} {{ partner.naksatra }}
              </li>

              <li :class="{ inRange: inMoonRange(partner.stridirgha) }">
                <h5
                  title="A Stridirgha azt mutatja, hogy a férfi energia mennyire tud áramolni a nő felé."
                >
                  Stridirgha
                </h5>

                <span title="Partner holdja">{{
                  store.zodiacs[partner.moon]
                }}</span>
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

                {{ getIcon(targetPerson.sex) }} {{ targetPerson.naksatra }}
                <br />
                {{ getIcon(partner.sex) }} {{ partner.naksatra }}

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
                  TODO Rashi
                </h5>

                {{ getIcon(targetPerson.sex) }} {{ targetPerson.moon }}
                <br />
                {{ getIcon(partner.sex) }} {{ partner.moon }}
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
        </ul>
      </section>
    </div>
  </div>
</template>

<style scoped>
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
