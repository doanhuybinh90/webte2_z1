<script setup lang="tsx">
import Input from "primevue/inputtext";
import Password from "primevue/password";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputOtp from "primevue/inputotp";

import { useUserStore } from "@/stores/user.store";

import { useToast } from "primevue/usetoast";
const toast = useToast();

import { router } from "@/router";

import {
  VerifyTwoFactorAuth,
  ValidateEmail,
  RegisterUser,
} from "@/helpers/API";
import { ref } from "vue";

const { qrCodeUrl } = defineProps(["qrCodeUrl"]);
const userStore = useUserStore();
const newUser = ref({
  username: "",
  email: "",
  password: "",
});

const confirmPassword = ref("");

const usernameInvalid = ref(false);
const emailInvalid = ref(false);
const emailExists = ref(false);
const passwordInvalid = ref(false);
const confirmPasswordInvalid = ref(false);

const validateRegisterForm = async () => {
  usernameInvalid.value = false;
  emailInvalid.value = false;
  passwordInvalid.value = false;
  confirmPasswordInvalid.value = false;
  emailExists.value = false;

  // Check if the username is empty
  if (
    newUser.value.username === "" ||
    newUser.value.username.length < 4 ||
    newUser.value.username.length > 128
  ) {
    usernameInvalid.value = true;
  }

  // Check if the email is empty or invalid
  if (
    newUser.value.email === "" ||
    !newUser.value.email.includes("@") ||
    newUser.value.email.length < 5 ||
    newUser.value.email.length > 128 ||
    /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(
      newUser.value.email
    ) === false
  ) {
    emailInvalid.value = true;
  }

  if ((await ValidateEmail(newUser.value.email)).exists) {
    emailExists.value = true;
  }

  // Check if the password is empty
  if (newUser.value.password === "" || newUser.value.password.length < 8) {
    passwordInvalid.value = true;
  }

  if (newUser.value.password !== confirmPassword.value) {
    confirmPasswordInvalid.value = true;
  }

  if (
    usernameInvalid.value ||
    emailInvalid.value ||
    passwordInvalid.value ||
    confirmPasswordInvalid.value ||
    emailExists.value
  ) {
    return;
  } else {
    dialogVisible.value = true;
  }
};

const dialogVisible = ref(false);
const user2faCode = ref("");
const user2faCodeInvalid = ref(false);

async function completeRegistration() {
  VerifyTwoFactorAuth(user2faCode.value).then(async (res) => {
    if (!res.verified) {
      user2faCodeInvalid.value = true;
    } else {
      await RegisterUser(newUser.value)
        .then(() => {
          toast.add({
            severity: "success",
            summary: "Success",
            detail: "Registration successful",
          });
          userStore.user.email = newUser.value.email;
          userStore.user.username = newUser.value.username;
          userStore.isLogged = true;
          localStorage.setItem("isLogged", "true");
          localStorage.setItem("email", newUser.value.email);
          localStorage.setItem("username", newUser.value.username!);
          dialogVisible.value = false;
          router.push("/login");
        })
        .catch((err) => {
          toast.add({
            severity: "error",
            summary: "Error",
            detail: err.message,
          });
        });
    }
  });
}
</script>

<template>
  <Dialog
    v-model:visible="dialogVisible"
    modal
    header="Google 2FA Authentication"
    :style="{ width: '25rem' }"
  >
    <div class="flex align-items-center gap-2 flex-column text-center">
      <div class="text-center">
        <img :src="qrCodeUrl" alt="QR Code" />
      </div>
      <span
        class="text-sm block"
        style="color: #f3c623; font-weight: thin; text-align: center"
      >
        It is required to scan the QR code with Google Authenticator to complete
        the registration
      </span>
      <div class="flex align-items-center justify-content-center">
        <a
          href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en&gl=US"
          class="h-fit"
        >
          <img
            src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png"
            alt="Get it on Google Play"
            class="h-4rem"
          />
        </a>
        <a
          href="https://apps.apple.com/us/app/google-authenticator/id388497605"
          class="h-fit"
        >
          <img
            src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg"
            alt="Download on the App Store"
            style="height: 2.7rem"
          />
        </a>
      </div>
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
        label="Complete Registration"
        @click="completeRegistration"
      ></Button>
    </div>
  </Dialog>
  <div
    class="w-full align-items-center md:justify-content-center flex-column flex"
    style="min-height: calc(100vh - 4rem)"
  >
    <div class="w-9 md:w-7">
      <h1 class="text-center">Register</h1>
      <div class="flex flex-column gap-2 pb-4">
        <div class="w-full">
          <Input
            v-model="newUser.username"
            placeholder="Username"
            :invalid="usernameInvalid"
            @input="usernameInvalid = false"
            class="w-full"
          />
          <span
            v-if="usernameInvalid"
            class="text-xs block"
            style="color: #f3c623; font-weight: thin"
          >
            Username must be between 4 and 32 characters
          </span>
        </div>
        <div>
          <Input
            v-model="newUser.email"
            placeholder="Email"
            :invalid="emailInvalid"
            @input="emailInvalid = false"
            class="w-full"
          />
          <span
            v-if="emailInvalid"
            class="text-xs block"
            style="color: #f3c623; font-weight: thin"
          >
            Email is invalid
          </span>
          <span
            v-if="emailExists"
            class="text-xs block"
            style="color: #f3c623; font-weight: thin"
          >
            Email already registered. Go to
            <span @click="router.push('/login')">Login</span>
          </span>
        </div>

        <div>
          <Password
            v-model="newUser.password"
            placeholder="Password"
            input-class="w-full"
            class="w-full"
            :invalid="passwordInvalid"
            @input="passwordInvalid = false"
            toggleMask
          />
          <span
            v-if="passwordInvalid"
            class="text-xs block"
            style="color: #f3c623; font-weight: thin"
          >
            Password must be at least 8 characters
          </span>
        </div>
        <div>
          <Password
            v-model="confirmPassword"
            placeholder="Confirm Password"
            :feedback="false"
            input-class="w-full"
            class="w-full"
            :invalid="confirmPasswordInvalid"
            @input="confirmPasswordInvalid = false"
            toggleMask
          />
          <span
            v-if="confirmPasswordInvalid"
            class="text-xs block"
            style="color: #f3c623; font-weight: thin"
          >
            Passwords do not match
          </span>
        </div>
      </div>
    </div>
    <div>
      <Button label="Register" @click="validateRegisterForm" />
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
