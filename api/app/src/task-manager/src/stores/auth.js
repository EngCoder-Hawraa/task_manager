import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: state => !!state.token
  },

  actions: {
    async register(userData) {
      try {
        const response = await fetch('http://localhost/task_manager/api/public/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(userData),
        })
        const data = await response.json()
        if (!response.ok) throw new Error(data.message || 'فشل التسجيل')
        this.token = data.token
        this.user = data.user
        // localStorage.setItem('token', data.token)

        return data // ✅ هذا هو السطر المفقود
      } catch (error) {
        console.error('حدث خطأ في التسجيل:', error)
        throw error
      }
    },

    async login({ email, password }) {
      try {
        const response = await fetch('http://localhost/task_manager/api/public/Auth/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password }),
        })
        const data = await response.json()
        // console.log('يرسل إلى السيرفر:', { email, password });
        // console.log('بيانات الاستجابة من السيرفر:', data)

        if (!response.ok) throw new Error(data.message || 'فشل تسجيل الدخول')
        this.token = data.token
        this.user = data.user
        localStorage.setItem('token', data.token)
        // console.log(response);

        return data // ✅ هذا هو السطر المفقود
      } catch (err) {
        throw new Error(err.message)
      }
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
    },
  },
})
