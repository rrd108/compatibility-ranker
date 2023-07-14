import { Sex } from '@/interfaces/Person'

const isMale = (sex: Sex) => ['férfi', 'ferfi', 'male'].includes(sex)

export default isMale
