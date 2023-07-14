import PersonMoonData from '@/interfaces/PersonMoonData'
import axios from 'axios'

const moonData = (person: PersonMoonData) => {
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

export default moonData
