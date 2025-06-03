<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />
  <AppSidebar ref="sidebarRef" />

  <v-container class="py-10">
    <transition name="fade-slide" appear>
      <v-card class="mx-auto px-4 pt-6 pb-8" elevation="10" rounded="xl">
        <!-- ✅ عنوان الصفحة -->
        <div class="text-center mb-6">
          <h2 class="text-h5 font-weight-bold mt-2" style="font-family: 'Cairo', sans-serif;">
            ➕ إضافة مهمة جديدة
          </h2>
        </div>

        <!-- ✅ النموذج -->
        <v-form @submit.prevent="submitTask" ref="formRef">
          <v-text-field
            v-model="title"
            label="✏️ العنوان"
            :error-messages="titleError"
            variant="outlined"
            density="comfortable"
            rounded="xl"
            class="mb-4"
            color="primary"
            required
          />

          <v-textarea
            v-model="description"
            label="📝 الوصف"
            :error-messages="descriptionError"
            variant="outlined"
            density="comfortable"
            rows="4"
            rounded="xl"
            class="mb-4"
            color="primary"
            required
          />

          <v-text-field
            v-model="due_date"
            label="📅 تاريخ الاستحقاق"
            type="date"
            :error-messages="dueDateError"
            variant="outlined"
            density="comfortable"
            rounded="xl"
            class="mb-4"
            color="primary"
            required
          />

          <v-select
            v-model="status"
            :items="statusOptions"
            label="الحالة"
            variant="outlined"
            density="comfortable"
            rounded="xl"
            class="mb-4"
            color="primary"
            required
          />

          <v-select
            v-model="priority"
            :items="priorityOptions"
            label="الأولوية"
            variant="outlined"
            density="comfortable"
            rounded="xl"
            class="mb-6"
            color="primary"
            required
          />

          <!-- ✅ زر الحفظ -->
          <v-btn type="submit" block color="primary" rounded="xl" class="text-white py-6" elevation="3">
            💾 حفظ المهمة
          </v-btn>
        </v-form>
      </v-card>
    </transition>
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
<style scoped>
.fade-slide-enter-active {
  transition: all 0.6s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.fade-slide-enter-to {
  opacity: 1;
  transform: translateY(0);
}

.v-card {
  background-color: #f9fbfd; /* خلفية فاتحة عصرية */
  border: 1px solid #e0e6ed;
}

.v-btn {
  font-family: 'Cairo', sans-serif;
  font-weight: bold;
  font-size: 1rem;
}
</style>

