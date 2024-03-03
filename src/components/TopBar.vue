<script setup lang="tsx">
import Button from "primevue/button";
import { router } from "@/router";
import { useUserStore } from "@/stores/user.store";
const userStore = useUserStore();
</script>

<template>
  <nav
    class="w-screen h-auto md:h-4rem p-2 px-4 flex justify-content-between flex-column md:flex-row gap-3 pb-4 md:pb-0"
    style="background-color: #18181b"
  >
    <div
      class="flex flex-row gap-3 md:gap-6 align-items-center flex-column md:flex-row"
    >
      <div
        class="flex align-items-center gap-2 cursor-pointer w-fit"
        @click="router.push('/')"
      >
        <img src="/logo.png" alt="logo" class="h-3rem" />
        <h3 class="m-0">Nobel Prize Laureates</h3>
      </div>
      <span
        class="font-bold cursor-pointer hover"
        @click="router.push('/laureates')"
      >
        Laureates
      </span>
      <span
        class="font-bold cursor-pointer hover"
        @click="router.push('/organisations')"
      >
        Organisations
      </span>
      <span
        class="font-bold cursor-pointer hover"
        @click="router.push('/admin')"
        v-if="userStore.isLogged"
      >
        Admin
      </span>
    </div>
    <div
      class="flex align-items-center align-self-center gap-2"
      v-if="!userStore.isLogged"
    >
      <Button label="Login" @click="router.push('/login')" />
      <Button label="Register" @click="router.push('/register')" />
    </div>
    <div class="flex align-items-center align-self-center gap-2" v-else>
      <span class="font-bold"> Welcome, {{ userStore.user.username }} </span>
      <Button label="Logout" @click="userStore.logout" />
    </div>
  </nav>
</template>

<style scoped>
.hover:hover {
  box-shadow: 0 2px 0 0 #f3c623;
}
</style>
