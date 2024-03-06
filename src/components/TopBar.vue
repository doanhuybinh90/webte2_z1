<script setup lang="tsx">
import Button from "primevue/button";
import { router } from "@/router";
import { useUserStore } from "@/stores/user.store";
const userStore = useUserStore();
</script>

<template>
  <nav
    class="w-full h-auto md:h-fit p-2 px-4 flex justify-content-between flex-column md:flex-row gap-3 pb-4 md:pb-0"
    style="background-color: #18181b"
  >
    <div
      class="flex flex-row gap-3 md:gap-6 align-items-center flex-column md:flex-row"
    >
      <div
        class="flex align-items-center gap-2 cursor-pointer w-fit"
        @click="router.push('/z1')"
      >
        <img src="/logo.png" alt="logo" class="h-3rem" />
        <h3 class="m-0">Nobel Prize Laureates</h3>
      </div>
      <span
        class="font-bold cursor-pointer hover"
        @click="router.push('/z1/laureates')"
      >
        Laureates
      </span>
      <span
        class="font-bold cursor-pointer hover"
        @click="router.push('/z1/organisations')"
      >
        Organisations
      </span>
      <span
        class="font-bold cursor-pointer hover"
        @click="router.push('/z1/admin')"
        v-if="userStore.user.isLogged"
      >
        Admin
      </span>
    </div>
    <div
      class="flex align-items-center align-self-center gap-2"
      v-if="!userStore.user.isLogged"
    >
      <Button label="Login" @click="router.push('/z1/login')" />
      <Button label="Register" @click="router.push('/z1/register')" />
    </div>
    <div class="flex align-items-center align-self-center gap-2" v-else>
      <span class="font-bold">
        Welcome,
        <span class="gradient-text">{{ userStore.user.username }}</span>
      </span>
      <Button label="Logout" @click="userStore.logout" />
    </div>
  </nav>
</template>

<style scoped>
.hover:hover {
  box-shadow: 0 2px 0 0 #f3c623;
}

.gradient-text {
  background: linear-gradient(
    74deg,
    rgb(66, 133, 244) 0px,
    rgb(155, 114, 203) 9%,
    rgb(217, 101, 112) 20%,
    rgb(217, 101, 112) 24%,
    rgb(155, 114, 203) 35%,
    rgb(66, 133, 244) 44%,
    rgb(155, 114, 203) 50%,
    rgb(217, 101, 112) 56%,
    rgb(217, 101, 112) 60%,
    rgb(155, 114, 203) 71%,
    rgb(66, 133, 244) 80%,
    rgb(155, 114, 203) 89%,
    rgb(217, 101, 112) 100%
  );
  color: #fff; /* White text for better contrast */
  padding: 5px; /* Add some padding for better readability */
  background-clip: text;
  animation: textShine 10s linear infinite;
  background-size: 500% auto;

  -webkit-text-fill-color: transparent;
}

@keyframes textShine {
  0% {
    background-position: 0% 50%;
  }
  100% {
    background-position: 100% 50%;
  }
}
</style>
