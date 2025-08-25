import api from './api'

export async function listPackages() {
  const { data } = await api.get('/packages')
  return data
}
