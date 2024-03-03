import { defineStore } from "pinia";
import { reactive, ref } from "vue";

export const useUserStore = defineStore("counter", () => {
  const user = reactive({
    username: localStorage.getItem("username") || "",
    email: localStorage.getItem("email") || "",
  });

  const isLogged = ref(localStorage.getItem("isLogged") === "true");

  const logout = () => {
    user.username = "";
    user.email = "";
    isLogged.value = false;
    localStorage.removeItem("username");
    localStorage.removeItem("email");
    localStorage.removeItem("isLogged");
  };

  return { user, isLogged, logout };
});
