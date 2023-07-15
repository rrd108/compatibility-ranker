export type Male = 'férfi' | 'ferfi' | 'male'
export type Female = 'nő' | 'no' | 'female'
export type Sex = Male | Female

export interface Person {
  id: number
  name: string
  sex: Sex
  birth_date: string
  birth_time: string
  birth_place: string
  info: string
}
