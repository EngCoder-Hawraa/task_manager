<template>
  <v-container class="py-4">
    <v-card elevation="2" class="pa-4">
      <v-card-title class="text-h6 text-center">➕ إضافة مهمة جديدة</v-card-title>
      <v-divider class="my-3"></v-divider>

      <v-form @submit.prevent="submitTask" ref="formRef">
        <v-text-field
          v-model="title"
          label="العنوان"
          required
          prepend-inner-icon="mdi-format-title"
          class="mb-3"
        ></v-text-field>

        <v-textarea
          v-model="description"
          label="الوصف"
          required
          prepend-inner-icon="mdi-text"
          class="mb-3"
          rows="3"
        ></v-textarea>

        <v-row>
          <v-col cols="12" md="6">
            <v-select
              v-model="status"
              :items="['open', 'in progress']"
              label="الحالة"
              prepend-inner-icon="mdi-play-circle-outline"
              required
              class="mb-3"
            ></v-select>
          </v-col>

          <v-col cols="12" md="6">
            <v-select
              v-model="priority"
              :items="['low', 'medium', 'high']"
              label="الأولوية"
              prepend-inner-icon="mdi-flag-outline"
              required
              class="mb-3"
            ></v-select>
          </v-col>
        </v-row>

        <v-text-field
          v-model="due_date"
          label="تاريخ الاستحقاق"
          type="date"
          prepend-inner-icon="mdi-calendar"
          required
          class="mb-4"
        ></v-text-field>

        <v-btn color="primary" type="submit" block>إضافة المهمة</v-btn>
      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import {ref} from 'vue'
import {useTaskStore} from '@/stores/taskStore'

const title = ref('')
const description = ref('')
const status = ref('open')
const priority = ref('medium')
const due_date = ref('')
const formRef = ref(null)

const taskStore = useTaskStore()

const submitTask = () => {
  taskStore.addTask({
    title: title.value,
    description: description.value,
    status: status.value,
    priority: priority.value,
    due_date: due_date.value
  })

  // إعادة تعيين القيم بعد الإضافة
  title.value = ''
  description.value = ''
  due_date.value = ''
  status.value = 'open'
  priority.value = 'medium'
}
</script>
