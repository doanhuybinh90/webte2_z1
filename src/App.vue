<script setup lang="tsx">
import TopBar from "@/components/TopBar.vue";
import Toast from "primevue/toast";
</script>

<template>
  <TopBar />
  <router-view />
  <Toast />
</template>

<style scoped></style>
