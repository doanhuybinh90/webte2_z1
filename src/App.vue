<script setup lang="tsx">
import TopBar from "@/components/TopBar.vue";
import Toast from "primevue/toast";
import { IsUserAuthorized } from "./helpers/API";
import { useUserStore } from "./stores/user.store";

window.addEventListener("visibilitychange", async () => {
  const { user, logout } = useUserStore();
  const data = await IsUserAuthorized();
  if (!data.isAuthorized) {
    logout();
  } else {
    user.username = data.username;
    user.email = data.email;
    user.isLogged = true;
  }
});
</script>

<template>
  <TopBar />
  <div class="pb-4">
    <router-view />
  </div>
  <Toast />
</template>

<style scoped></style>
