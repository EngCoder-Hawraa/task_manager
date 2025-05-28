<template>
  <v-container class="py-4">
    <!-- زر فتح النموذج -->
    <v-btn color="primary" @click="dialog = true" prepend-icon="mdi-plus">
      إضافة مهمة جديدة
    </v-btn>

    <!-- نافذة المودال -->
    <v-dialog v-model="dialog" max-width="600">
      <v-card>
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

            <!-- يمكنك إضافة حقول status و priority هنا إذا أردت -->

            <v-text-field
              v-model="due_date"
              label="تاريخ الاستحقاق"
              type="date"
              :error-messages="dueDateError"
              required
              class="mb-4"
            />

            <!-- زر الإرسال -->
            <v-btn type="submit" color="success" block>
              حفظ المهمة
            </v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import {ref} from 'vue'
import {useTaskStore} from '@/stores/taskStore'
import {useToast} from 'vue-toastification'

// المتغيرات التفاعلية
const dialog = ref(false)
const title = ref('')
const description = ref('')
const status = ref('open')
const priority = ref('medium')
const due_date = ref('')

// مراجع الأخطاء والتنبيهات
const formRef = ref(null)
const toast = useToast()

const titleError = ref('')
const descriptionError = ref('')
const dueDateError = ref('')

// استدعاء المتجر
const taskStore = useTaskStore()

// دالة التحقق من صحة الحقول
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

// دالة إرسال المهمة مع استخدام التنبيهات
const submitTask = async () => {
  if (!validateFields()) {
    toast.warning('⚠️ الرجاء تعبئة جميع الحقول المطلوبة', {
      position: 'top-right',
      timeout: 3000
    })
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
      toast.error(`❌ حدث خطأ: ${taskStore.error}`, {
        position: 'top-right',
        timeout: 4000
      })
    } else {
      toast.success('✅ تم إضافة المهمة بنجاح', {
        position: 'top-right',
        timeout: 3000
      })

      // إعادة تعيين الحقول وإغلاق المودال
      title.value = ''
      description.value = ''
      due_date.value = ''
      status.value = 'open'
      priority.value = 'medium'
      dialog.value = false
    }
  } catch (error) {
    toast.error('⚠️ فشل في الاتصال بالخادم', {
      position: 'top-right',
      timeout: 4000
    })
    console.error(error)
  }
}
</script>
