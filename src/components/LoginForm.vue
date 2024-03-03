<script setup lang="tsx">
import Input from "primevue/inputtext";
import Password from "primevue/password";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputOtp from "primevue/inputotp";

import { useUserStore } from "@/stores/user.store";
const userStore = useUserStore();
import { useToast } from "primevue/usetoast";
const toast = useToast();

import { router } from "@/router";

import { ValidateLogin, ValidateOtp } from "@/helpers/API";
import { ref } from "vue";

const user = ref({
  login: "",
  password: "",
});

const validateLoginForm = async () => {
  loginInvalid.value = false;
  const loginCheck = await ValidateLogin(user.value.login, user.value.password);
  if (loginCheck.isCorrect === false) {
    toast.add({
      severity: "error",
      summary: "Login failed",
      detail: loginCheck.message,
    });
    loginInvalid.value = true;
    return;
  } else {
    dialogVisible.value = true;
  }
};

const completeLogin = async () => {
  user2faCodeInvalid.value = false;
  const otpCheck = await ValidateOtp(user.value.login, user2faCode.value);
  if (otpCheck.isCorrect === false) {
    user2faCodeInvalid.value = true;
    return;
  }
  toast.add({
    severity: "success",
    summary: "Login successful",
    detail: "You have successfully logged in",
  });
  userStore.isLogged = true;
  userStore.user.email = user.value.login;
  userStore.user.username = otpCheck.username!;
  localStorage.setItem("isLogged", "true");
  localStorage.setItem("email", user.value.login);
  localStorage.setItem("username", otpCheck.username!);
  router.push("/");
};

const loginInvalid = ref(false);

const dialogVisible = ref(false);
const user2faCode = ref("");
const user2faCodeInvalid = ref(false);
</script>

<template>
  <Dialog
    v-model:visible="dialogVisible"
    modal
    header="Google 2FA Authentication"
    :style="{ width: '25rem' }"
  >
    <div class="flex align-items-center gap-2 flex-column text-center">
      <span class="p-text-secondary block mb-2 text-center">
        Enter the code from the application.
      </span>
      <InputOtp v-model="user2faCode" :length="6">
        <template #default="{ attrs, events }">
          <input
            type="text"
            v-bind="attrs"
            v-on="events"
            class="custom-otp-input"
          />
        </template>
      </InputOtp>
      <span
        v-if="user2faCodeInvalid"
        class="text-xs block"
        style="color: #f3c623; font-weight: thin"
      >
        Invalid code
      </span>
    </div>
    <div class="flex justify-content-end gap-2 pt-4">
      <Button
        type="button"
        label="Cancel"
        severity="secondary"
        @click="dialogVisible = false"
      >
      </Button>
      <Button
        type="button"
        label="Complete Login"
        @click="completeLogin"
      ></Button>
    </div>
  </Dialog>
  <div
    class="w-full align-items-center md:justify-content-center flex-column flex"
    style="min-height: calc(100vh - 4rem)"
  >
    <div class="w-9 md:w-7">
      <h1 class="text-center">Login</h1>
      <div class="flex flex-column gap-2 pb-4">
        <Input
          v-model="user.login"
          placeholder="Login (email)"
          :invalid="loginInvalid"
          @input="loginInvalid = false"
          class="w-full"
        />

        <Password
          v-model="user.password"
          placeholder="Password"
          input-class="w-full"
          class="w-full"
          :feedback="false"
          :invalid="loginInvalid"
          @input="loginInvalid = false"
          toggleMask
        />
      </div>
    </div>
    <div>
      <Button label="Login" @click="validateLoginForm" />
    </div>
  </div>
</template>

<style scoped>
.custom-otp-input {
  width: 40px;
  font-size: 36px;
  border: 0 none;
  appearance: none;
  text-align: center;
  transition: all 0.2s;
  background: transparent;
  border-bottom: 2px solid var(--surface-500);
}

.custom-otp-input:focus {
  outline: 0 none;
  border-bottom-color: var(--primary-color);
}
</style>
