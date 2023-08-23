import { createRouter, createWebHistory } from "vue-router";
import LandingView from "@/views/LandingView.vue";
import LoginView from "@/views/LoginView.vue";
import LocationView from "@/views/LocationView.vue";
import MapView from "@/views/MapView.vue";
import TripView from "@/views/TripView.vue";
import axios from "axios";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "login",
      component: LoginView,
    },
    {
      path: "/landing",
      name: "landing",
      component: LandingView,
    },
    {
      path: "/location",
      name: "location",
      component: LocationView,
    },
    {
      path: "/map",
      name: "map",
      component: MapView,
    },
    {
      path: "/trip",
      name: "trip",
      component: TripView,
    },
    // {
    //   path: '/about',
    //   name: 'about',
    //   // route level code-splitting
    //   // this generates a separate chunk (About.[hash].js) for this route
    //   // which is lazy-loaded when the route is visited.
    //   component: () => import('../views/AboutView.vue')
    // }
  ],
});

router.beforeEach((to) => {
  if (to.name === "login") {
    return true;
  }

  if (!localStorage.getItem("token")) {
    return { name: "login" };
  }

  checkTokenAuthenticity();
});

const checkTokenAuthenticity = () => {
  axios
    .get(`${import.meta.env.VITE_API_URL}/user`, {
      headers: {
        Authorization: "Bearer " + localStorage.getItem("token"),
      },
    })
    .then((response) => {
      console.log(response.data);
    })
    .catch((error) => {
      localStorage.removeItem("token");
      router.push({ name: "login" });
    });
};

export default router;
