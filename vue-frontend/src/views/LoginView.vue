<script setup>
import { reactive } from 'vue'
import { vMaska } from 'maska'
import axios from 'axios'

const credentials = reactive({
  phone: '',
})

const handleLogin = () => {
  axios.post('http://localhost:8000/api/login', {
    phone: credentials.phone.replaceAll(" ", ''),
  })
    .then((response) => {
      console.log(response);
    }).catch((error) => {
      console.log(error.response.data);
      alert(error.response.data.message);
    })
}
</script>

<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Enter your phone number</h1>

    <form action="#" @submit.prevent="handleLogin">
      <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <input type="text" v-model="credentials.phone" v-maska data-maska="+234 ### ### ####" name="phone" id="phone"
              placeholder="+2349030609267"
              class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" />
          </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button type="submit"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">Continue</button>
        </div>
      </div>
    </form>
  </div>
</template>

<style lang="scss" scoped></style>
