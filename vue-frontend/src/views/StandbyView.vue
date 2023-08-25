
<template>
  <div>
    <div class="pt-16">
      <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
      <div v-if="!trip.id" class="mt-8 flex justify-center">
        <Loader />
      </div>

      <div v-else>
        <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
          <div class="bg-white px-4 py-5 sm:p-6">
            <div>
              <GMapMap ref="gMap" :zoom="14" :center="trip.destination" style="width: 100%; height: 256px;">
              </GMapMap>
            </div>

            <div class="mt-2">
              <div class="text-xl">Going to <strong>{{ trip.destination_name }}</strong> </div>
            </div>
          </div>

          <div class="flex justify-between bg-gray-5o px-4 py-3 text-right sm:px-6">
            <button :loading="loading" type="button"
              class="inline-flex justify-center rounded-md border border-transparent bg-red-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:outline-none">
              <Loading v-if="loading" :active="loading" text="Canceling Trip" />

              <span v-else>Decline Trip</span>
            </button>

            <button :loading="loading" type="button"
              class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
              <Loading v-if="loading" :active="loading" text="Accepting Trip" />

              <span v-else>Accept Trip</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import Loader from '../components/Loader.vue';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { useTripStore } from '../stores/trip';

const title = ref('Waiting for ride request...')
const trip = useTripStore();

onMounted(async () => {
  console.log("mounted");
  try {
    let echo = new Echo({
      broadcaster: 'pusher',
      key: `${import.meta.env.VITE_PUSHER_APP_KEY}`,
      cluster: 'mt1',
      wsHost: window.location.hostname,
      wsPort: 6001,
      forceTLS: false,
      disableStats: true,
      enabledTransports: ['ws', 'wss']
    })

    echo.channel('drivers')
      .listen('TripCreated', (e) => {
        title.value = 'Ride requested:'

        trip.$patch(e.trip)
        console.log('TripCreated', e)
        console.log('Trip', trip)
      })

  } catch (error) {
    console.log(error);
  }
});

</script>