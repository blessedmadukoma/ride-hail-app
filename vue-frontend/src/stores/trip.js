import { ref, reactive } from "vue";
import { defineStore } from "pinia";

export const useTripStore = defineStore("trip", () => {
  const id = ref(null);
  const user_id = ref(null);
  const destination_name = ref("");

  const origin = reactive({
    lat: null,
    lng: null,
  });

  const destination = reactive({
    lat: null,
    lng: null,
  });
  return { id, user_id, origin, destination, destination_name };
});
