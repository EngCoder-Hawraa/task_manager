import { defineStore } from 'pinia'
import axios from 'axios'

// تعيين الـ base URL لمرة واحدة فقط (لاحظ حذف "TaskController" في المسار)
axios.defaults.baseURL = 'http://localhost/task_manager/api/public/TaskController'

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
    async fetchAllTasks(status = null, priority = null) {
      try {
        const params = {};
        if (status && status !== 'الكل') params.status = status;
        if (priority && priority !== 'الكل') params.priority = priority;

        const res = await axios.get('/Tasks', { params });

        // ✅ تحقق من أن res.data عبارة عن مصفوفة
        this.tasks = Array.isArray(res.data) ? res.data : [];
        // console.log(res.data)
      } catch (err) {
        this.error = err.message;
        this.tasks = [];
      }
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
      // console.log('🧪 ID:', id)
      this.error = null
      try {
        const res = await axios.put(
          `/update/${id}`,
        updatedTask,
          {
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${this.token}`
            }
          }
        )
        if (res.data.success) await this.fetchTasks()
      } catch (err) {
        console.error(err)
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
