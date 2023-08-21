<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { vMaska } from 'maska'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const credentials = reactive({
  phone: '',
  login_code: null,
})
const waitingOnVerification = ref(false);

// run once the component is mounted i.e. loaded
onMounted(() => {
  if (localStorage.getItem('token')) {
    router.push({ name: 'landing' });
  }
})

// generate a reactive property based on the value of another reactive property
const formattedCredentials = computed(() => {
  return {
    ...credentials,
    phone: credentials.phone.replaceAll(" ", ''),
    login_code: credentials.login_code
  }
})

const handleLogin = () => {
  axios.post('http://localhost:8000/api/login', formattedCredentials.value)
    .then((response) => {
      console.log(response);
      waitingOnVerification.value = true;
    }).catch((error) => {
      console.log(error.response.data);
      alert(error.response.data.message);
    })
}


const handleVerification = () => {
  axios.post('http://localhost:8000/api/login/verify', formattedCredentials.value).then((response) => {
    console.log(response.data);

    localStorage.setItem('token', response.data);

    router.push({ name: 'landing' });
  }).catch((error) => {
    console.log(error.response.data);
  })
}
</script>

<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Enter your phone number</h1>

    <form v-if="!waitingOnVerification" action="#" @submit.prevent="handleLogin">
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

    <form v-else action="#" @submit.prevent="handleVerification">
      <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <input type="text" v-model="credentials.login_code" v-maska data-maska="######" name="login_code"
              id="login_code" placeholder="123456"
              class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none" />
          </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button type="submit"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">Verify</button>
        </div>
      </div>
    </form>
  </div>
</template>

<style lang="scss" scoped></style>
