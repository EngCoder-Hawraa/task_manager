<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-container class="py-4 mt-6">
    <v-card class="mx-auto">
      <v-card-title class="text-h6 text-center">➕ إضافة مهمة جديدة</v-card-title>
      <v-divider class="my-3"></v-divider>

      <v-card-text>
        <v-form @submit.prevent="submitTask" ref="formRef">
          <v-text-field
            v-model="title"
            label="العنوان"
            :error-messages="titleError"
            required
            class="mb-3"
          />

          <v-textarea
            v-model="description"
            label="الوصف"
            :error-messages="descriptionError"
            required
            rows="3"
            class="mb-3"
          />

          <v-text-field
            v-model="due_date"
            label="تاريخ الاستحقاق"
            type="date"
            :error-messages="dueDateError"
            required
            class="mb-4"
          />

          <!-- 🟢 حقل الحالة -->
          <v-select
            v-model="status"
            :items="statusOptions"
            label="الحالة"
            required
            class="mb-4"
          />

          <!-- 🔵 حقل الأولوية -->
          <v-select
            v-model="priority"
            :items="priorityOptions"
            label="الأولوية"
            required
            class="mb-4"
          />

          <v-btn type="submit" color="success" block>
            حفظ المهمة
          </v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import { useToast } from 'vue-toastification'
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";

// المتغيرات الخاصة بالنموذج
const title = ref('')
const description = ref('')
const due_date = ref('')
const status = ref('مفتوحة')
const priority = ref('متوسطة')

// الخيارات للحالة والأولوية
const statusOptions = ['مفتوحة', 'قيد التنفيذ', 'مكتملة', 'تم الإلغاء']
const priorityOptions = ['منخفضة', 'متوسطة', 'عالية']

// أخطاء الحقول
const titleError = ref('')
const descriptionError = ref('')
const dueDateError = ref('')
const formRef = ref(null)

const toast = useToast()
const taskStore = useTaskStore()
const sidebarRef = ref(null)

const validateFields = () => {
  let valid = true
  titleError.value = ''
  descriptionError.value = ''
  dueDateError.value = ''

  if (!title.value.trim()) {
    titleError.value = 'حقل العنوان مطلوب'
    valid = false
  }

  if (!description.value.trim()) {
    descriptionError.value = 'حقل الوصف مطلوب'
    valid = false
  }

  if (!due_date.value) {
    dueDateError.value = 'تاريخ الاستحقاق مطلوب'
    valid = false
  }

  return valid
}

const submitTask = async () => {
  if (!validateFields()) {
    toast.warning('⚠️ الرجاء تعبئة جميع الحقول المطلوبة')
    return
  }

  try {
    await taskStore.addTask({
      title: title.value,
      description: description.value,
      status: status.value,
      priority: priority.value,
      due_date: due_date.value
    })

    if (taskStore.error) {
      toast.error(`❌ حدث خطأ: ${taskStore.error}`)
    } else {
      toast.success('✅ تم إضافة المهمة بنجاح')
      title.value = ''
      description.value = ''
      due_date.value = ''
      status.value = 'open'
      priority.value = 'medium'
    }
  } catch (err) {
    console.error(err)
    toast.error('⚠️ فشل في الاتصال بالخادم')
  }
}

// ✅ التحكم في فتح/إغلاق السايدبار
function toggleDrawer() {
  if (sidebarRef.value?.toggleDrawer) {
    sidebarRef.value.toggleDrawer()
  }
}
</script>
