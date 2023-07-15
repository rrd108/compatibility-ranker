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
  rajjuInfo: { area: string; effect: string; icon: string }[]
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
      Ashwini: 'láb',
      Ashlesha: 'láb',
      Magha: 'láb',
      Jyeshta: 'láb',
      Moola: 'láb',
      Revati: 'láb',

      Bharani: 'csípő',
      Pushya: 'csípő',
      'Purva Phalguni': 'csípő',
      Anuradha: 'csipő',
      'Purva Ashadha': 'csípő',
      'Uttara Bhadrapada': 'csípő',

      Rohini: 'nyak',
      Ardra: 'nyak',
      Hasta: 'nyak',
      Swati: 'nyak',
      Shravana: 'nyak',
      Shatabhisha: 'nyak',

      Krithika: 'köldök',
      Punarvasu: 'köldök',
      'Uttara Phalguni': 'köldök',
      Vishaka: 'köldök',
      'Uttara Ashadha': 'köldök',
      'Purva Bhadrapada': 'köldök',

      Mrigashirsha: 'fej',
      Chitra: 'fej',
      Dhanishta: 'fej',
    },
    rajjuInfo: [
      //ha mindkettő
      { area: 'láb', effect: 'állandó vándorlás', icon: 'walking' },
      { area: 'csípő', effect: 'szegénység', icon: 'money-bill-alt' },
      { area: 'köldök', effect: 'gyerek elvesztése', icon: 'baby' },
      { area: 'nyak', effect: 'feleség halála', icon: 'female' },
      { area: 'fej', effect: 'férj halála', icon: 'male' },
    ],
    rasi: [{}],
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
