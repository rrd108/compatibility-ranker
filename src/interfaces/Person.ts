export type Male = 'férfi' | 'ferfi' | 'male'
export type Female = 'nő' | 'no' | 'female'

export interface Person {
  id: number
  name: string
  sex: Male | Female
  birth_date: string
  birth_time: string
  birth_place: string
  info: string
}
