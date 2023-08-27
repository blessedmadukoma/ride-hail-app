<script setup>
import { useRouter } from 'vue-router'
import http from '../helpers/http';
import { ref } from 'vue';
import Loading from '../components/Loading.vue';
import toggleLoading from '../helpers/loading';

const router = useRouter()

const loading = ref(false);

const handleFindARide = () => {
  router.push({ name: 'location' });
}

const handleStartDriving = () => {
  toggleLoading(loading);
  http().get('/driver').then((response) => {
    // if the authenticated user is a driver
    if (response.data.driver) {
      toggleLoading(loading);
      router.push({ name: 'standby' });
    } else {
      // else
      router.push({ name: 'driver' });
    }
  }).catch((error) => {
    console.log(error.response.data);
  })
}
</script>

<template>
  <main class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Ridaar</h1>
    <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
      <div class="bg-white px-4 py-5 sm:p-6">
        <div class="flex justify-between">

          <button @click="handleStartDriving" :loading="loading" type="button"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
            <Loading v-if="loading" :active="loading" text="Loading..." />

            <span v-else>Start Driving</span>
          </button>

          <button @click="handleFindARide" type="submit"
            class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">Find
            A Ride</button>
        </div>
      </div>
    </div>
  </main>
</template>
