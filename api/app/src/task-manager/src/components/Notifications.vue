<template>
  <!-- ✅ رأس الصفحة -->
  <AppHeader @toggle-sidebar="toggleDrawer" />

  <!-- ✅ السايدبار -->
  <AppSidebar ref="sidebarRef" />
  <v-container class="py-5">
    <h1 class="text-h5 font-weight-bold mb-3">🔔 التنبيهات</h1>
    <v-alert type="info" v-if="notifications.length === 0">
      لا توجد تنبيهات حالية.
    </v-alert>

    <v-list v-else>
      <v-list-item
        v-for="(notification, index) in notifications"
        :key="index"
        class="mb-2"
        :class="notification.read ? 'bg-grey-lighten-4' : 'bg-grey-lighten-3'"
      >
        <v-list-item-avatar>
          <v-icon :color="notification.read ? 'grey' : 'primary'">
            mdi-bell
          </v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>{{ notification.title }}</v-list-item-title>
          <v-list-item-subtitle class="text-caption">
            {{ notification.time }} - {{ notification.type }}
          </v-list-item-subtitle>
        </v-list-item-content>

        <v-list-item-action>
          <v-btn icon @click="markAsRead(index)">
            <v-icon>mdi-check</v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-container>
</template>
<script setup>
import { ref } from 'vue'
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";

const notifications = ref([
  {
    title: '📌 تم تعيين مهمة جديدة: تصميم واجهة',
    type: 'مهام',
    time: 'قبل 10 دقائق',
    read: false
  },
  {
    title: '⚠️ المهمة "إعداد تقرير" تجاوزت الموعد النهائي',
    type: 'تنبيه',
    time: 'منذ ساعتين',
    read: false
  },
  {
    title: '✅ تم إكمال مهمة "مراجعة المحتوى"',
    type: 'تحديث',
    time: 'أمس',
    read: true
  },
  {
    title: '📣 إشعار إداري: اجتماع الفريق غدًا الساعة 10 صباحًا',
    type: 'إداري',
    time: 'منذ 3 أيام',
    read: true
  }
])

const markAsRead = (index) => {
  notifications.value[index].read = true
}
</script>
<style scoped>
.v-list-item {
  border-radius: 12px;
  transition: background-color 0.3s ease;
}
</style>
