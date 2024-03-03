<script setup lang="tsx">
import { onMounted, ref } from "vue";
import { GetTwoFactorAuthQR } from "@/helpers/API";

import Logo from "@/components/Logo.vue";
import RegisterForm from "@/components/RegisterForm.vue";

const qrCodeUrl = ref("");

onMounted(async () => {
  await GetTwoFactorAuthQR().then((res) => {
    qrCodeUrl.value = res.qrCodeUrl;
  });
});
</script>

<template>
  <div class="w-full h-full flex" style="min-height: calc(100vh - 4rem)">
    <div class="hidden md:block w-6">
      <Logo />
    </div>
    <div class="w-full md:w-6">
      <RegisterForm :qr-code-url="qrCodeUrl" />
    </div>
  </div>
</template>
