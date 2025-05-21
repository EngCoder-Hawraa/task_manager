import { defineStore } from 'pinia'
import axios from 'axios'

axios.defaults.baseURL = 'http://localhost/task_manager/api/public'

export const useTaskStore = defineStore('task', {
  state: () => ({
    tasks: [],
    selectedTask: null,
    loading: false,
    error: null,
    token: localStorage.getItem('token') || ''
  }),
  actions: {
    setToken(token) {
      this.token = token
      localStorage.setItem('token', token)
    },
    async fetchTasks() {
      this.loading = true
      this.error = null
      this.tasks = []

      try {
        const res = await axios.get('/index', {
          headers: { Authorization: `Bearer ${this.token}` }
        })

        this.tasks = res.data
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      } finally {
        this.loading = false
      }
    },
    async addTask(taskData) {
      try {
        const res = await axios.post('/api/dashboard', taskData, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    },
    async updateTask(id, updatedTask) {
      try {
        const res = await axios.put(`/api/dashboard/${id}`, updatedTask, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    },
    async deleteTask(id) {
      try {
        const res = await axios.delete(`/api/dashboard/${id}`, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    },
    async markAsDone(id) {
      try {
        const res = await axios.patch(`/api/dashboard/done/${id}`, {}, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    }
  }
})
