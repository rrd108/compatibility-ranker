import { defineStore } from 'pinia'
import Zodiacs from './interfaces/Zodiacs'
import { Naksatra } from './interfaces/Naksatra'
import Vedas from './interfaces/Vedas'
import PersonMoonData from './interfaces/PersonMoonData'
import Rajjus from './interfaces/Rajjus'

interface State {
  loading: boolean
  maxPoints: number
  naksatras: Naksatra[]
  rajjus: Rajjus
  rajjuIcons: {}
  targetPerson: PersonMoonData
  token: string
  vedas: Vedas
  zodiacs: Zodiacs
}

export const useStore = defineStore('cR', {
  state: (): State => ({
    loading: false,
    maxPoints: 35,
    naksatras: [
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
    ],
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
      'Purva Ashadha': 'csípő',
      Pushya: 'csípő',
      Revati: 'láb',
      Rohini: 'nyak',
      Shatabhisha: 'nyak',
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
    targetPerson: {} as PersonMoonData,
    token: '',
    vedas: {
      // these naksatras must not be married together
      Ashwini: 'Jyeshta',
      Punarvasu: 'Uttara Ashadha',
      'Uttara Phalguni': 'Purva Bhadrapada',
      Bharani: 'Anuradha',
      Pushya: 'Purva Ashadha',
      Hasta: 'Shatabhisha',
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
  }),
})
