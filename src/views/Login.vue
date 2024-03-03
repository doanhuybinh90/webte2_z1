<script setup lang="tsx">
import Logo from "@/components/Logo.vue";
import LoginForm from "@/components/LoginForm.vue";
</script>

<template>
  <div class="w-full h-full flex" style="min-height: calc(100vh - 4rem)">
    <div class="hidden md:block w-6">
      <Logo />
    </div>
    <div class="w-full md:w-6">
      <LoginForm />
    </div>
  </div>
</template>
