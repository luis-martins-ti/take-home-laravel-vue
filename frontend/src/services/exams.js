import api from "./api"

export async function searchExams(query) {
  const { data } = await api.get(`/exams?search=${query}`)
  return data
}
