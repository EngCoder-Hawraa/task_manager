import axios from 'axios'

const API = 'http://localhost/task-manager/api/auth'

export default {
  register(data) {
    return axios.post(`${API}/register`, data)
  },
  login(data) {
    return axios.post(`${API}/login`, data)
  }
}
