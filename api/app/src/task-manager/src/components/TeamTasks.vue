<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-container class="py-5">
    <!-- عنوان الصفحة -->
    <v-row class="align-center mb-4">
      <v-col>
        <h1 class="text-h5 font-weight-bold">📋 المهام الجماعية</h1>
        <p class="text-body-2 text-grey">إدارة وتوزيع المهام بين أعضاء الفريق</p>
      </v-col>
    </v-row>

    <!-- شريط البحث والتصفية -->
    <v-row class="mb-4">
      <v-col cols="12" sm="6">
        <v-text-field v-model="search" label="🔍 ابحث عن مهمة..." clearable />
      </v-col>
      <v-col cols="12" sm="3">
        <v-select :items="statusFilterOptions" v-model="statusFilter" label="الحالة" />
      </v-col>
      <v-col cols="12" sm="3">
        <v-select :items="priorityFilterOptions" v-model="priorityFilter" label="الأولوية" />
      </v-col>
    </v-row>

    <!-- جدول المهام -->
    <v-data-table
      :headers="headers"
      :items="filteredTasks"
      item-value="id"
      class="elevation-1"
    >
      <template #item.assigned_to="{ item }">
        <v-avatar v-for="member in item.assigned_to" :key="member.id" size="24" class="mr-1">
          <img :src="member.avatar" alt="avatar" />
        </v-avatar>
      </template>

      <template #item.actions="{ item }">
        <v-btn icon @click="viewTask(item)"><v-icon>mdi-eye</v-icon></v-btn>
        <v-btn icon color="primary" @click="editTask(item)"><v-icon>mdi-pencil</v-icon></v-btn>
        <v-btn icon color="error" @click="deleteTask(item.id)"><v-icon>mdi-delete</v-icon></v-btn>
      </template>
    </v-data-table>

    <!-- زر إضافة -->
    <v-btn color="success" class="mt-4" @click="goToAddPage">
      <v-icon left>mdi-plus</v-icon> إضافة مهمة جماعية
    </v-btn>
  </v-container>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";

// بيانات البحث والتصفية
const search = ref('')
const statusFilter = ref(null)
const priorityFilter = ref(null)


const statusFilterOptions = ['مفتوحة', 'قيد التنفيذ', 'مكتملة', 'تم الإلغاء']
const priorityFilterOptions = ['عالية', 'متوسطة', 'منخفضة']
const sidebarRef = ref(null)

// بيانات المهام (مؤقتة لاختبار الواجهة)
const tasks = ref([
  {
    id: 1,
    title: 'تصميم واجهة الموقع',
    description: 'تصميم واجهة الصفحة الرئيسية',
    status: 'قيد التنفيذ',
    priority: 'عالية',
    assigned_to: [
      { id: 1, avatar: 'https://i.pravatar.cc/40?u=user1' },
      { id: 2, avatar: 'https://i.pravatar.cc/40?u=user2' }
    ],
    comments: [
      { id: 1, user: 'أحمد', text: 'يرجى تعديل الألوان.' },
      { id: 2, user: 'سارة', text: 'تم تنفيذ المطلوب.' }
    ]
  },
  {
    id: 2,
    title: 'كتابة المحتوى',
    description: 'محتوى صفحة من نحن',
    status: 'مفتوحة',
    priority: 'متوسطة',
    assigned_to: [
      { id: 3, avatar: 'https://i.pravatar.cc/40?u=user3' }
    ],
    comments: []
  }
])

const headers = [
  { text: 'العنوان', value: 'title' },
  { text: 'الوصف', value: 'description' },
  { text: 'الحالة', value: 'status' },
  { text: 'الأولوية', value: 'priority' },
  { text: 'المسؤولون', value: 'assigned_to' },
  { text: 'الإجراءات', value: 'actions', sortable: false }
]

// فلترة المهام حسب البحث والتصفية
const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    const matchesSearch = task.title.includes(search.value) || task.description.includes(search.value)
    const matchesStatus = !statusFilter.value || task.status === statusFilter.value
    const matchesPriority = !priorityFilter.value || task.priority === priorityFilter.value
    return matchesSearch && matchesStatus && matchesPriority
  })
})

// دوال الإجراءات (مجرد أمثلة)
const viewTask = (task) => {
  const comments = task.comments?.length
    ? task.comments.map(c => `- ${c.user}: ${c.text}`).join('\n')
    : 'لا توجد تعليقات بعد.'
  alert(`عرض المهمة: ${task.title}\n\nالتعليقات:\n${comments}`)
}
const editTask = (task) => {
  alert(`تعديل المهمة: ${task.title}`)
}
const deleteTask = (id) => {
  tasks.value = tasks.value.filter(task => task.id !== id)
}
const goToAddPage = () => {
  alert('الانتقال إلى صفحة إضافة مهمة جديدة')
}

// ✅ التحكم في فتح/إغلاق السايدبار
function toggleDrawer() {
  if (sidebarRef.value?.toggleDrawer) {
    sidebarRef.value.toggleDrawer()
  }
}
</script>

<style scoped>
.text-grey {
  color: #757575;
}
</style>
