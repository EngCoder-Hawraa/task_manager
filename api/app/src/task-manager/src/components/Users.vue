<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-container class="py-5">
    <!-- عنوان الصفحة -->
    <v-row class="align-center mb-4">
      <v-col>
        <h1 class="text-h5 font-weight-bold">👥 إدارة المستخدمين</h1>
        <p class="text-body-2 text-grey">إضافة وتحديث وحذف المستخدمين مع صلاحياتهم</p>
      </v-col>
    </v-row>

    <!-- شريط البحث والتصفية -->
    <v-row class="mb-4">
      <v-col cols="12" sm="4">
        <v-text-field v-model="search" label="🔍 ابحث عن مستخدم..." clearable />
      </v-col>
      <v-col cols="12" sm="4">
        <v-select :items="roles" v-model="roleFilter" label="الدور" clearable />
      </v-col>
      <v-col cols="12" sm="4">
        <v-select :items="statusOptions" v-model="statusFilter" label="الحالة" clearable />
      </v-col>
    </v-row>

    <!-- جدول المستخدمين -->
    <v-data-table
      :headers="headers"
      :items="filteredUsers"
      item-value="id"
      class="elevation-1"
    >
      <template #item.avatar="{ item }">
        <v-avatar size="32">
          <img :src="item.avatar" alt="avatar" />
        </v-avatar>
      </template>

      <template #item.actions="{ item }">
        <v-btn icon @click="viewUser(item)"><v-icon>mdi-eye</v-icon></v-btn>
        <v-btn icon color="primary" @click="editUser(item)"><v-icon>mdi-pencil</v-icon></v-btn>
        <v-btn icon color="error" @click="deleteUser(item.id)"><v-icon>mdi-delete</v-icon></v-btn>
      </template>
    </v-data-table>

    <!-- زر الإضافة -->
    <v-btn color="primary" class="mt-4" @click="goToAddUser">
      <v-icon left>mdi-plus</v-icon> مستخدم جديد
    </v-btn>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";

const search = ref('')
const roleFilter = ref(null)
const statusFilter = ref(null)

const roles = ['مدير', 'موظف', 'مشرف', 'زائر']
const statusOptions = ['مفعل', 'غير مفعل']

const users = ref([
  {
    id: 1,
    name: 'أحمد علي',
    email: 'ahmed@example.com',
    role: 'مدير',
    status: 'مفعل',
    avatar: 'https://i.pravatar.cc/40?u=ahmed'
  },
  {
    id: 2,
    name: 'سارة محمد',
    email: 'sara@example.com',
    role: 'موظف',
    status: 'غير مفعل',
    avatar: 'https://i.pravatar.cc/40?u=sara'
  }
])

const headers = [
  { text: 'الصورة', value: 'avatar', sortable: false },
  { text: 'الاسم', value: 'name' },
  { text: 'البريد الإلكتروني', value: 'email' },
  { text: 'الدور', value: 'role' },
  { text: 'الحالة', value: 'status' },
  { text: 'الإجراءات', value: 'actions', sortable: false }
]

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch =
      user.name.includes(search.value) || user.email.includes(search.value)
    const matchesRole = !roleFilter.value || user.role === roleFilter.value
    const matchesStatus = !statusFilter.value || user.status === statusFilter.value
    return matchesSearch && matchesRole && matchesStatus
  })
})

const viewUser = (user) => {
  alert(`عرض المستخدم: ${user.name}`)
}
const editUser = (user) => {
  alert(`تعديل المستخدم: ${user.name}`)
}
const deleteUser = (id) => {
  if (confirm('هل أنت متأكد أنك تريد حذف هذا المستخدم؟')) {
    users.value = users.value.filter(u => u.id !== id)
  }
}
const goToAddUser = () => {
  alert('الانتقال إلى صفحة إضافة مستخدم جديد')
}
</script>

<style scoped>
.text-grey {
  color: #757575;
}
</style>
