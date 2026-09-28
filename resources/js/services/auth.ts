import axios from './axios'

export async function login(email: string, password: string) {
  await axios.get('/sanctum/csrf-cookie')

  const response = await axios.post('/api/login', {
    email,
    password,
  })

  return response.data
}

export async function getMe() {
  const response = await axios.get('/api/me')

  return response.data
}

export async function logout() {
  const response = await axios.post('/api/logout')

  return response.data
}