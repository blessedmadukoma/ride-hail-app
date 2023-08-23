import { ref, computed, reactive } from "vue";
import { defineStore } from "pinia";

const getUserLocation = () => {
  return new Promise((response, reject) => {
    navigator.geolocation.getCurrentPosition(response, reject);
  });
};

export const useLocationStore = defineStore("location", () => {
  const destination = reactive({
    name: "",
    address: "",
    geometry: {
      lat: null,
      lng: null,
    },
  });

  const current = reactive({
    geometry: {
      lat: null,
      lng: null,
    },
  });

  const updateCurrentLocation = async () => {
    const userLocation = await getUserLocation();
    current.geometry = {
      lat: userLocation.coords.latitude,
      lng: userLocation.coords.longitude,
    };
  };
  return { destination, current, updateCurrentLocation };
});
