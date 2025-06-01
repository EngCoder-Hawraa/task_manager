<template>
  <v-app>
    <v-main>
        <!-- ✅ رأس الصفحة -->
        <AppHeader @toggle-sidebar="toggleDrawer" />
        <AppSidebar ref="sidebarRef" />
        <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
import AppHeader from "@/components/AppHeader.vue";
import AppSidebar from "@/components/AppSidebar.vue";
</script>
<style>
* {
  direction: rtl;
  margin: 0;
}
.v-input--density-default .v-field--variant-solo, .v-input--density-default .v-field--variant-solo-inverted, .v-input--density-default .v-field--variant-solo-filled, .v-input--density-default .v-field--variant-filled {
  direction: rtl;
}
</style>
