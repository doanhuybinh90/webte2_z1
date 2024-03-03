import { createRouter, createWebHistory } from "vue-router";
import Laureates from "@/views/Laureates.vue";
import LaureatInfo from "./views/LaureatInfo.vue";
import Home from "./views/Home.vue";
import Login from "./views/Login.vue";
import Register from "./views/Register.vue";
import Organisations from "./views/Organisations.vue";

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      component: Home,
    },
    {
      path: "/laureates",
      component: Laureates,
    },
    {
      path: "/organisations",
      component: Organisations,
    },
    {
      path: "/login",
      component: Login,
    },
    {
      path: "/register",
      component: Register,
    },
    {
      path: "/laureates/:id(\\d+)",
      component: LaureatInfo,
      props: (route) => ({ id: route.params.id }),
    },
  ],
});
