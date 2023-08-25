<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Driving to passenger...</h1>
    <div>
      <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
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
          <button v-if="trip.is_started"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
            Complete Trip</button>

          <button v-else
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
            Passenger Picked Up</button>
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

const location = useLocationStore();
const trip = useTripStore();

const gMap = ref(null);
const intervalRef = ref(null);

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
