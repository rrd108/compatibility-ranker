import { Naksatra } from './Naksatra'
import { Person } from './Person'

export default interface PersonMoonData extends Person {
  naksatra: Naksatra
  moon:
    | 'Aries'
    | 'Taurus'
    | 'Gemini'
    | 'Cancer'
    | 'Leo'
    | 'Virgo'
    | 'Libra'
    | 'Scorpio'
    | 'Sagittarius'
    | 'Capricorn'
    | 'Aquarius'
    | 'Pisces'
}
