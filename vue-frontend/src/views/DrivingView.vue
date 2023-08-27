<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
    <div>
      <div v-if="!trip.is_complete" class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <GMapMap ref="gMap" :zoom="14" :center="location.current.geometry" style="width: 100%; height: 256px;">
              <GMapMarker :position="location.current.geometry" :icon="currentIcon" />
              <GMapMarker :position="location.destination.geometry" :icon="destinationIcon" />
            </GMapMap>
          </div>

          <div class="mt-2">
            <div class="text-xl">Going to pick up <strong>a passenger</strong> </div>
          </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button v-if="trip.is_started" @click="handleCompleteTrip" :loading="loadingCompleteTrip" type="button"
            class="cursor-pointer inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
            <Loading v-if="loadingCompleteTrip" :active="loadingCompleteTrip" text="Completing Trip" />

            <span v-else>Start Trip</span>
          </button>

          <button v-else @click="handleStartTrip" :loading="loadingStartTrip" type="button"
            class="cursor-pointer inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
            <Loading v-if="loadingStartTrip" :active="loadingStartTrip" text="Starting Trip" />

            <span v-else>Start Trip</span>
          </button>
        </div>
      </div>

      <div v-else class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
        <div class="bg-white px-4 py-5 sm:p-6">
          <RideComplete />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, onUnmounted } from 'vue';
import { useLocationStore } from '../stores/location';
import http from '../helpers/http';
import { useTripStore } from '../stores/trip';
import RideComplete from '../components/RideComplete.vue';
import { useRouter } from 'vue-router';
import toggleLoading from '../helpers/loading';
import Loading from '../components/Loading.vue';

const location = useLocationStore();
const trip = useTripStore();

const router = useRouter();

const loadingStartTrip = ref(false);
const loadingCompleteTrip = ref(false);
const gMap = ref(null);
const intervalRef = ref(null);
const title = ref('Driving to passenger...')

const currentIcon = {
  url: 'https://openmoji.org/data/color/svg/1F698.svg',
  scaledSize: {
    width: 24,
    height: 24,
  }
}

const destinationIcon = {
  url: 'https://openmoji.org/data/color/svg/1F920.svg',
  scaledSize: {
    width: 24,
    height: 24,
  }
}

const broadcastDriverLocation = () => {
  http().post(`/trip/${trip.id}/location`, {
    driver_location: location.current.geometry,
  })
    .then((response) => { })
    .catch((error) => {
      console.error(error);
    })
}

const updateMapBounds = (mapObject) => {
  let originPoint = new google.maps.LatLng(location.current.geometry),
    destinationPoint = new google.maps.LatLng(location.destination.geometry),
    bounds = new google.maps.LatLngBounds();

  bounds.extend(originPoint);
  bounds.extend(destinationPoint);

  mapObject.fitBounds(bounds);
}

const handleStartTrip = () => {
  toggleLoading(loadingStartTrip);
  http().post(`/trip/${trip.id}/start`)
    .then((response) => {
      // trip.is_started = true;
      title.value = 'Traveling to destination...'
      location.$patch({
        destination: {
          name: response.data.destination_name,
          geometry: response.data.destination,
        }
      })

      toggleLoading(loadingStartTrip);
      trip.$patch(response.data)
    })
    .catch((error) => {
      console.error(error);
    })
}

const handleCompleteTrip = () => {
  toggleLoading(loadingCompleteTrip);
  http().post(`/trip/${trip.id}/end`)
    .then((response) => {
      // trip.is_completed = true;
      title.value = 'Trip completed!'

      trip.$patch(response.data)

      setTimeout(() => {
        trip.reset();
        location.reset();
        toggleLoading(loadingCompleteTrip);

        router.push({ name: 'standby' })
      }, 3000);
    })
    .catch((error) => {
      console.error(error);
    })
}

onMounted(() => {
  console.log("Trip:", trip);
  gMap.value.$mapPromise.then((mapObject) => {
    updateMapBounds(mapObject)

    intervalRef.value = setInterval(async () => {
      // update the driver's current position and update map bounds
      await location.updateCurrentLocation()

      // update the driver's position in the database
      broadcastDriverLocation()

      updateMapBounds(mapObject)
    }, 5000)
  })
});

onUnmounted(() => {
  clearInterval(intervalRef.value);

  intervalRef.value = null;
})
</script>
