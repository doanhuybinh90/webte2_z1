import { createRouter, createWebHistory } from "vue-router";
import Laureates from "@/views/Laureates.vue";
import LaureatInfo from "./views/LaureatInfo.vue";
import Home from "./views/Home.vue";
import Login from "./views/Login.vue";
import Register from "./views/Register.vue";
import Organisations from "./views/Organisations.vue";
import Admin from "./views/Admin.vue";

import { IsUserAuthorized } from "@/helpers/API";
import { useUserStore } from "./stores/user.store";

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/z1",
      component: Home,
    },
    {
      path: "/z1/laureates",
      component: Laureates,
    },
    {
      path: "/z1/organisations",
      component: Organisations,
    },
    {
      path: "/z1/login",
      component: Login,
      beforeEnter: async (_to, _from, next) => {
        if ((await IsUserAuthorized()).isAuthorized) {
          next("/");
        } else {
          next();
        }
      },
    },
    {
      path: "/z1/register",
      component: Register,
      beforeEnter: async (_to, _from, next) => {
        if ((await IsUserAuthorized()).isAuthorized) {
          next("/");
        } else {
          next();
        }
      },
    },
    {
      path: "/z1/admin",
      component: Admin,
      beforeEnter: async (_to, _from, next) => {
        if ((await IsUserAuthorized()).isAuthorized) {
          next();
        } else {
          next("/login");
        }
      },
    },
    {
      path: "/z1/laureates/:id(\\d+)",
      component: LaureatInfo,
      props: (route) => ({ id: route.params.id }),
    },
  ],
});

router.beforeEach(async () => {
  const { user } = useUserStore();
  const data = await IsUserAuthorized();
  if (data.isAuthorized) {
    user.username = data.username;
    user.email = data.email;
    user.isLogged = true;
  }
});
