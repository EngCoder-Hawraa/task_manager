<template>
  <v-app>
    <v-main>
      <v-container>
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
</script>
<style>
* {
  direction: rtl;
}
.v-input--density-default .v-field--variant-solo, .v-input--density-default .v-field--variant-solo-inverted, .v-input--density-default .v-field--variant-solo-filled, .v-input--density-default .v-field--variant-filled {
  direction: rtl;
}
</style>
