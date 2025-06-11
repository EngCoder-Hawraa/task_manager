<template>
  <v-dialog v-model="dialog" max-width="700" persistent>
    <v-card class="rounded-xl elevation-10">
      <v-card-title class="text-h5 font-weight-bold d-flex align-center">
        <v-icon color="primary" class="me-2">mdi-plus-circle</v-icon>
        إضافة مهمة جماعية
      </v-card-title>

      <v-divider />

      <v-card-text>
        <v-form @submit.prevent="submitTask" ref="formRef" lazy-validation>
          <v-text-field
            v-model="title"
            label="✏️ عنوان المهمة"
            required
            variant="outlined"
            :rules="[v => !!v || 'حقل العنوان مطلوب']"
            class="mb-4"
          />

          <v-textarea
            v-model="description"
            label="📝 وصف المهمة"
            rows="3"
            required
            variant="outlined"
            :rules="[v => !!v || 'حقل الوصف مطلوب']"
            class="mb-4"
          />

          <v-row dense>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="dueDate"
                label="📅 تاريخ الاستحقاق"
                type="date"
                required
                variant="outlined"
                :rules="[v => !!v || 'تاريخ الاستحقاق مطلوب']"
                class="mb-4"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="priority"
                :items="['منخفضة', 'متوسطة', 'مرتفعة']"
                label="🚦 الأولوية"
                required
                variant="outlined"
                class="mb-4"
              />
            </v-col>
          </v-row>

          <v-row dense>
            <v-col cols="12" sm="6">
              <v-select
                v-model="status"
                :items="['مفتوحة', 'قيد التنفيذ', 'مكتملة']"
                label="📌 الحالة"
                required
                variant="outlined"
                class="mb-4"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="assignedUsers"
                :items="userList"
                item-title="name"
                item-value="id"
                label="👥 الأعضاء المعينون"
                multiple
                chips
                variant="outlined"
                class="mb-4"
              />
            </v-col>
          </v-row>

          <v-file-input
            v-model="attachments"
            label="📎 المرفقات (اختياري)"
            multiple
            variant="outlined"
            show-size
            truncate-length="15"
            class="mb-6"
          />

          <v-card-actions class="justify-end">
            <v-btn text color="error" @click="closeDialog">إلغاء</v-btn>
            <v-btn color="primary" type="submit" elevation="2">💾 حفظ المهمة</v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, watch } from 'vue'

// التحكم في ظهور dialog من خارج المكون
const props = defineProps({
  modelValue: Boolean,
  userList: {
    type: Array,
    default: () => [
      { id: 1, name: 'أحمد' },
      { id: 2, name: 'سارة' },
      { id: 3, name: 'حسن' }
    ]
  }
})
const emit = defineEmits(['update:modelValue', 'submit-task'])

const dialog = ref(props.modelValue)

watch(() => props.modelValue, val => (dialog.value = val))
watch(dialog, val => emit('update:modelValue', val))

const title = ref('')
const description = ref('')
const dueDate = ref('')
const priority = ref('متوسطة')
const status = ref('مفتوحة')
const assignedUsers = ref([])
const attachments = ref([])

const formRef = ref(null)

function resetForm() {
  title.value = ''
  description.value = ''
  dueDate.value = ''
  priority.value = 'متوسطة'
  status.value = 'مفتوحة'
  assignedUsers.value = []
  attachments.value = []
  if (formRef.value) formRef.value.resetValidation()
}

function closeDialog() {
  resetForm()
  dialog.value = false
}

async function submitTask() {
  if (!formRef.value.validate()) return

  const payload = {
    title: title.value,
    description: description.value,
    due_date: dueDate.value,
    priority: priority.value,
    status: status.value,
    assigned_users: assignedUsers.value,
    attachments: attachments.value
  }

  emit('submit-task', payload)
  closeDialog()
}
</script>

<style scoped>
.v-card-title {
  font-family: 'Cairo', sans-serif;
  font-weight: 700;
}
</style>
