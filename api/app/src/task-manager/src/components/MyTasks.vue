<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />

  <v-container class="py-9">
    <v-row>
      <v-col cols="12">
        <!-- عنوان الصفحة -->
        <div class="d-flex align-center justify-space-between mb-6">
          <h2 class="text-h5 font-weight-bold">📋 مهامي</h2>
          <v-chip size="small" color="primary" class="px-3" text-color="white">
            {{ filteredTasks.length }} مهمة
          </v-chip>
        </div>

        <!-- فلاتر الحالة -->
        <v-btn-toggle
          v-model="selectedStatus"
          class="mb-6"
          rounded
          density="comfortable"
          color="primary"
          divided
        >
          <v-btn value="all">الكل</v-btn>
          <v-btn value="مفتوحة">مفتوحة</v-btn>
          <v-btn value="قيد التنفيذ">قيد التنفيذ</v-btn>
          <v-btn value="مكتملة">مكتملة</v-btn>
          <v-btn value="تم الإلغاء">تم الإلغاء</v-btn>
        </v-btn-toggle>

        <!-- قائمة المهام -->
        <v-slide-y-transition group>
          <v-card
            v-for="task in paginatedTasks"
            :key="task.id"
            class="mb-4 pa-4 elevation-2 rounded-xl"
          >
            <v-row>
              <v-col cols="12" sm="8">
                <h3 class="text-h6 font-weight-medium mb-1">{{ task.title }}</h3>
                <p class="text-body-2 text-grey-darken-1 mb-2">{{ task.description }}</p>
                <v-chip :color="statusColor(task.status)" size="small" class="mt-1" text-color="white">
                  {{ task.status }}
                </v-chip>
              </v-col>

              <v-col cols="12" sm="4" class="d-flex justify-end align-center flex-wrap">
                <v-tooltip text="تعديل المهمة" location="top">
                  <template #activator="{ props }">
                    <v-btn icon color="primary" class="me-2" v-bind="props" @click="openEditDialog(task)">
                      <v-icon size="22">mdi-pencil</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>

                <v-tooltip text="حذف المهمة" location="top">
                  <template #activator="{ props }">
                    <v-btn icon color="red" v-bind="props" @click="confirmDeleteTask(task)">
                      <v-icon size="22">mdi-delete</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>
              </v-col>
            </v-row>
          </v-card>
        </v-slide-y-transition>

        <!-- لا توجد مهام -->
        <v-alert
          v-if="filteredTasks.length === 0"
          type="info"
          border="start"
          color="blue-lighten-4"
          class="mt-10 text-center"
        >
          لا توجد مهام ضمن هذا التصنيف.
        </v-alert>

        <!-- Pagination -->
        <v-pagination
          v-model="currentPage"
          :length="totalPages"
          :total-visible="5"
          color="primary"
          size="large"
          rounded
          class="custom-pagination mt-4 justify-center"
          prev-icon="mdi-chevron-right"
          next-icon="mdi-chevron-left"
        />
      </v-col>
    </v-row>

    <!-- ✅ مكون تعديل المهمة -->
    <EditMyTask
      v-if="editDialog"
      :task="taskToEdit"
      @close="closeEditDialog"
      @updated="onTaskUpdated"
    />

    <!-- ✅ مودال تأكيد الحذف -->
    <v-dialog v-model="deleteDialog" max-width="400" persistent>
      <v-card class="rounded-xl elevation-12 pa-4">
        <v-card-title class="text-h6 font-weight-bold d-flex align-center justify-center">
          <v-icon color="red" class="me-2" size="28">mdi-alert-circle-outline</v-icon>
          تأكيد حذف المهمة
        </v-card-title>

        <v-card-text class="text-center">
          هل أنت متأكد من حذف المهمة:
          <strong class="d-block mt-2">{{ taskToDelete?.title }}</strong>
          <br />
          هذا الإجراء لا يمكن التراجع عنه.
        </v-card-text>

        <v-card-actions class="justify-center mt-4">
          <v-btn color="grey lighten-1" rounded text @click="deleteDialog = false">إلغاء</v-btn>
          <v-btn color="red darken-1" rounded text @click="deleteTaskConfirmed">حذف</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import EditMyTask from './EditMyTask.vue'
import AppHeader from "@/components/AppHeader.vue"
import AppSidebar from "@/components/AppSidebar.vue"
import Swal from 'sweetalert2'

const taskStore = useTaskStore()

const selectedStatus = ref('all')
const currentPage = ref(1)
const itemsPerPage = ref(5)

const editDialog = ref(false)
const taskToEdit = ref(null)
const sidebarRef = ref(null)

const deleteDialog = ref(false)
const taskToDelete = ref(null)

// ✅ تحميل المهام عند بداية الصفحة
onMounted(() => {
  taskStore.fetchTasks()
})

// ✅ إعادة تعيين الصفحة عند تغيير الفلتر
watch(selectedStatus, () => {
  currentPage.value = 1
})

// ✅ تصفية المهام
const filteredTasks = computed(() => {
  if (selectedStatus.value === 'all') return taskStore.tasks
  return taskStore.tasks.filter(task => task.status === selectedStatus.value)
})

// ✅ تقسيم المهام لصفحات
const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredTasks.value.slice(start, start + itemsPerPage.value)
})

// ✅ حساب عدد الصفحات
const totalPages = computed(() => {
  return Math.ceil(filteredTasks.value.length / itemsPerPage.value)
})

// ✅ ألوان الحالة
function statusColor(status) {
  switch (status) {
    case 'مفتوحة': return 'blue'
    case 'قيد التنفيذ': return 'orange'
    case 'مكتملة': return 'green'
    case 'تم الإلغاء': return 'red'
    default: return 'grey'
  }
}

// ✅ تعديل المهمة
function openEditDialog(task) {
  taskToEdit.value = task
  editDialog.value = true
}

function closeEditDialog() {
  editDialog.value = false
  taskToEdit.value = null
}

function onTaskUpdated() {
  taskStore.fetchTasks()
  closeEditDialog()
}

// ✅ تأكيد الحذف
function confirmDeleteTask(task) {
  taskToDelete.value = task
  deleteDialog.value = true
}

async function deleteTaskConfirmed() {
  if (!taskToDelete.value) return

  try {
    await taskStore.deleteTask(taskToDelete.value.id)
    deleteDialog.value = false
    taskToDelete.value = null
    taskStore.fetchTasks()

    // ✅ عرض رسالة نجاح
    Swal.fire({
      icon: 'success',
      title: 'تم الحذف!',
      text: 'تم حذف المهمة بنجاح.',
      confirmButtonText: 'حسنًا',
      confirmButtonColor: '#3085d6'
    })
  } catch (error) {
    console.error('حدث خطأ أثناء الحذف:', error)

    // ❌ عرض رسالة خطأ
    Swal.fire({
      icon: 'error',
      title: 'خطأ!',
      text: 'حدث خطأ أثناء حذف المهمة.',
      confirmButtonText: 'موافق',
      confirmButtonColor: '#d33'
    })
  }
}


// ✅ التحكم في السايدبار
function toggleDrawer() {
  if (sidebarRef.value?.toggleDrawer) {
    sidebarRef.value.toggleDrawer()
  }
}
</script>

<style scoped>
h2 {
  font-family: 'Cairo', sans-serif;
}
</style>
