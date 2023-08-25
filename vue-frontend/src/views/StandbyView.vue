
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
            <button @click="handleDeclineTrip" type="button"
              class="cursor-pointer inline-flex justify-center rounded-md border border-transparent bg-red-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:outline-none">
              <span>Decline Trip</span>
            </button>

            <button @click="handleAcceptTrip" :loading="loading" type="button"
              class="cursor-pointer inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
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
import { useLocationStore } from '../stores/location';
import http from '../helpers/http';
import { useRouter } from 'vue-router';
import toggleLoading from '../helpers/loading';
import Loading from '../components/Loading.vue';

const title = ref('Waiting for ride request...')
const gMap = ref(null);
const loading = ref(false);

const trip = useTripStore();
const location = useLocationStore();

const router = useRouter();

onMounted(async () => {
  await location.updateCurrentLocation();

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
        console.log("TripCreated:", e.trip);
        setTimeout(initMapDirections, 2000);
      })

  } catch (error) {
    console.log(error);
  }
});

const initMapDirections = () => {
  // draw path on the map
  gMap.value.$mapPromise.then((mapObject) => {
    let originPoint = new google.maps.LatLng(trip.origin),
      destinationPoint = new google.maps.LatLng(trip.destination),
      directionsService = new google.maps.DirectionsService(),
      directionsDisplay = new google.maps.DirectionsRenderer({
        map: mapObject,
      });

    directionsService.route({
      origin: originPoint,
      destination: destinationPoint,
      avoidTolls: false,
      avoidHighways: false,
      travelMode: google.maps.TravelMode.DRIVING
    }, (res, status) => {
      if (status === google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(res)
      } else {
        console.error(status);
      }
    })
  })
}

const handleDeclineTrip = () => {
  trip.reset();
  title.value = 'Waiting for ride request...'
}

const handleAcceptTrip = () => {
  toggleLoading(loading)
  http().post(`/trip/${trip.id}/accept`, {
    driver_location: location.current.geometry,
  }).then((response) => {
    console.log(response.data);
    toggleLoading(loading)
    location.$patch({
      destination: {
        name: 'Passenger',
        geometry: response.data.origin,
      }
    });
    router.push({ name: 'driving' })
  }).catch((error) => {
    console.error(error);
  })
}

</script>