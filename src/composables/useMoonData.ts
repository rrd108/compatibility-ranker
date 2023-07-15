import PersonMoonData from '@/interfaces/PersonMoonData'
import axios from 'axios'
import { useStore } from '@/store'

const moonData = (person: PersonMoonData) => {
  if (!person) return

  const store = useStore()
  store.loading = true
  axios
    .get(`${import.meta.env.VITE_APP_API_URL}?moonData=${person.id}`, {
      headers: { Authorization: `ApiKey ${store.token}` },
    })
    .then(response => {
      store.loading = false
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
              naksatra: person.naksatra.split(',')[0],
              pada: person.naksatra.split(',')[1],
              moon: person.moon,
            },
            { headers: { Authorization: `ApiKey ${store.token}` } }
          )
          .then(response => console.log(response.data))
          .catch(error => console.error(error))
      }
    })
    .catch(error => console.error(error))
}

export default moonData
