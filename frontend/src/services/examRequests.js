import api from './api'

export async function listExamRequests(page = 1) {
  const { data } = await api.get(`/exam-requests?page=${page}`)
  return data
}

export async function getExamRequest(id) {
  const { data } = await api.get(`/exam-requests/${id}`)
  return data
}

export async function createExamRequest(payload) {
  const { data } = await api.post('/exam-requests', payload)
  return data
}
