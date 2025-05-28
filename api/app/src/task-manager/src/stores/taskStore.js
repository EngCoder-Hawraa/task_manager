import { defineStore } from 'pinia'
import axios from 'axios'

// تعيين الـ base URL لمرة واحدة فقط (لاحظ حذف "Dashboard" في المسار)
axios.defaults.baseURL = 'http://localhost/task_manager/api/public/Dashboard'

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
        // console.log(res.data);
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      } finally {
        this.loading = false
      }
    },

    async addTask(taskData) {
      this.error = null
      try {
        const res = await axios.post('/store', taskData, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) {
          await this.fetchTasks()
        } else {
          this.error = res.data.message || 'حدث خطأ أثناء الإضافة'
        }
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    },

    async updateTask(id, updatedTask) {
      this.error = null
      try {
        const res = await axios.put(`/update/${id}`, updatedTask, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    },

    async deleteTask(id) {
      this.error = null
      try {
        const res = await axios.delete(`/destroy/${id}`, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    },

    async markAsDone(id) {
      this.error = null
      try {
        const res = await axios.patch(`/done/${id}`, {}, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      }
    }
  }
})
