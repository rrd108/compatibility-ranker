import { Sex } from '@/interfaces/Person'
import isMale from './useIsMale'

const getIcon = (sex: Sex) => (isMale(sex) ? '🧔' : '👰')

export default getIcon
