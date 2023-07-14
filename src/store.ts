import { defineStore } from 'pinia'

interface State {
  token: string
}

export const useStore = defineStore('cR', {
  state: (): State => ({
    token: '',
  }),
})
