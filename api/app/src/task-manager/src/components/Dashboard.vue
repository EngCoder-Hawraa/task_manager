<template>
  <div>
    <v-row>
      <v-col cols="12" md="4">
        <v-card color="blue-lighten-4">
          <v-card-title>إجمالي المهام</v-card-title>
          <v-card-text>{{ totalTasks }}</v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card color="green-lighten-4">
          <v-card-title>المهام المكتملة</v-card-title>
          <v-card-text>{{ completedTasks }}</v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card color="red-lighten-4">
          <v-card-title>المهام المتبقية</v-card-title>
          <v-card-text>{{ pendingTasks }}</v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-divider class="my-4" />

    <h3>آخر المهام:</h3>
    <v-list>
      <v-list-item v-for="task in latestTasks" :key="task.id">
        <v-list-item-content>
          <v-list-item-title>{{ task.title }}</v-list-item-title>
          <v-list-item-subtitle>{{ task.status }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const totalTasks = ref(0)
const completedTasks = ref(0)
const pendingTasks = ref(0)
const latestTasks = ref([])

onMounted(async () => {
  const res = await fetch('http://localhost/task_manager/api/dashboard.php', {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })

  const data = await res.json()

  totalTasks.value = data.total
  completedTasks.value = data.completed
  pendingTasks.value = data.pending
  latestTasks.value = data.latest
})
</script>
