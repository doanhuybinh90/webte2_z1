import { router } from "@/router";
import { defineStore } from "pinia";
import { reactive } from "vue";

import { Logout } from "@/helpers/API";

export const useUserStore = defineStore("user", () => {
  const user = reactive({
    username: localStorage.getItem("username") || "",
    email: localStorage.getItem("email") || "",
    isLogged: localStorage.getItem("isLogged") === "true",
  });

  const logout = async () => {
    await Logout();

    user.username = "";
    user.email = "";
    user.isLogged = false;
    localStorage.removeItem("username");
    localStorage.removeItem("email");
    localStorage.removeItem("isLogged");
    router.push("/z1/login");
  };

  return { user, logout };
});
