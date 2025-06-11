<template>
  <AppHeader @toggle-sidebar="toggleDrawer" />
  <AppSidebar ref="sidebarRef" />

  <v-container class="py-5">
    <v-row class="align-center mb-4">
      <v-col>
        <h1 class="text-h5 font-weight-bold">📋 المهام الجماعية</h1>
        <p class="text-body-2 text-grey">إدارة وتوزيع المهام بين أعضاء الفريق</p>
      </v-col>

      <v-col cols="auto">
        <v-btn color="primary" class="me-4" @click="dialog = true">
          <v-icon left>mdi-plus</v-icon> إضافة مهمة جماعية
        </v-btn>
        <AddTeamTask
          v-model="dialog"
          :userList="users"
          @submit-task="handleTaskSubmit"
        />
      </v-col>
    </v-row>

    <v-row class="mb-4" align="center">
      <v-col cols="12" sm="6">
        <v-text-field
          v-model="search"
          label="🔍 ابحث عن مهمة..."
          clearable
          outlined
          dense
        />
      </v-col>

      <v-col cols="12" sm="3">
        <v-select
          v-model="statusFilter"
          :items="statusFilterOptions"
          label="الحالة"
          outlined
          dense
        />
      </v-col>

      <v-col cols="12" sm="3">
        <v-select
          v-model="priorityFilter"
          :items="priorityFilterOptions"
          label="الأولوية"
          outlined
          dense
        />
      </v-col>
    </v-row>

    <v-data-table
      :headers="headers"
      :items="filteredTasks"
      item-value="id"
      class="elevation-1"
      dense
      :loading="loading"
      loading-text="جارٍ التحميل..."
      show-header
      disable-pagination
      hide-default-footer
    >
      <template #item.assigned_to="{ item }">
        <v-avatar
          v-for="member in item.assigned_to"
          :key="member.id"
          size="24"
          class="me-1"
          :title="member.name"
        >
          <img :src="member.avatar" alt="avatar" />
        </v-avatar>
      </template>

      <template #item.actions="{ item }">
        <v-btn icon @click="viewTask(item)" :title="'عرض ' + item.title">
          <v-icon>mdi-eye</v-icon>
        </v-btn>
        <v-btn icon color="primary" @click="editTask(item)" :title="'تعديل ' + item.title">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn icon color="error" @click="deleteTask(item.id)" :title="'حذف ' + item.title">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </v-container>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import AppHeader from '@/components/AppHeader.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import AddTeamTask from '@/components/AddTeamTask.vue'

// حالة ظهور dialog إضافة مهمة جماعية
const dialog = ref(false)

// البحث وفلترة الحالة والأولوية
const search = ref('')
const statusFilter = ref('الكل')
const priorityFilter = ref('الكل')

// قائمة المستخدمين - يمكن تعديلها لجلبها من API
const users = ref([
  { id: 1, name: 'أحمد', avatar: 'https://i.pravatar.cc/150?img=1' },
  { id: 2, name: 'سارة', avatar: 'https://i.pravatar.cc/150?img=2' },
  { id: 3, name: 'حسن', avatar: 'https://i.pravatar.cc/150?img=3' }
])

const statusFilterOptions = ['الكل', 'مفتوحة', 'قيد التنفيذ', 'مكتملة', 'تم الإلغاء']
const priorityFilterOptions = ['الكل', 'عالية', 'متوسطة', 'منخفضة']

const sidebarRef = ref(null)
const taskStore = useTaskStore()

const loading = ref(false)

// تحميل المهام عند تركيب المكون
const loadTasks = async () => {
  loading.value = true
  await taskStore.fetchAllTasks()
  loading.value = false
}

onMounted(loadTasks)

// إعادة تحميل المهام عند تغيير الفلاتر
watch([statusFilter, priorityFilter], async () => {
  loading.value = true
  await taskStore.fetchAllTasks(
    statusFilter.value === 'الكل' ? null : statusFilter.value,
    priorityFilter.value === 'الكل' ? null : priorityFilter.value
  )
  loading.value = false
})

// فلترة المهام محلياً بناء على البحث في العنوان أو الوصف
const filteredTasks = computed(() => {
  if (!search.value.trim()) return taskStore.tasks
  const keyword = search.value.trim().toLowerCase()
  return taskStore.tasks.filter(task =>
    task.title.toLowerCase().includes(keyword) ||
    task.description.toLowerCase().includes(keyword)
  )
})

// تعريف أعمدة الجدول
const headers = [
  { text: 'العنوان', value: 'title' },
  { text: 'الوصف', value: 'description' },
  { text: 'الحالة', value: 'status' },
  { text: 'الأولوية', value: 'priority' },
  { text: 'المسؤولون', value: 'assigned_to' },
  { text: 'الإجراءات', value: 'actions', sortable: false }
]

// دوال التعامل مع المهام
const viewTask = (task) => {
  const comments = task.comments?.length
    ? task.comments.map(c => `- ${c.name}: ${c.comment}`).join('\n')
    : 'لا توجد تعليقات بعد.'
  alert(`عرض المهمة: ${task.title}\n\nالتعليقات:\n${comments}`)
}

const editTask = (task) => {
  alert(`تعديل المهمة: ${task.title}`)
}

const deleteTask = async (id) => {
  const confirmed = confirm('هل أنت متأكد من حذف هذه المهمة؟')
  if (!confirmed) return

  await taskStore.deleteTask(id)
}

// استقبال مهمة جديدة من الـ dialog وإضافتها
const handleTaskSubmit = async (taskData) => {
  loading.value = true
  const result = await taskStore.addTask(taskData)
  loading.value = false

  if (result?.success) {
    alert('تم إضافة المهمة بنجاح!')
  } else {
    alert('حدث خطأ أثناء إضافة المهمة')
  }
}

// تبديل القائمة الجانبية
const toggleDrawer = () => {
  sidebarRef.value?.toggleDrawer?.()
}
</script>

<style scoped>
.text-grey {
  color: #757575;
}

.me-1 {
  margin-right: 4px;
}

.me-4 {
  margin-right: 16px;
}
</style>
