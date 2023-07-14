import { defineStore } from 'pinia'
import Zodiacs from './interfaces/Zodiacs'

interface State {
  loading: boolean
  token: string
  zodiacs: Zodiacs
}

export const useStore = defineStore('cR', {
  state: (): State => ({
    loading: false,
    token: '',
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
