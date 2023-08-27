<template>
  <div v-if="active"
    class="inline-flex justify-center rounded-md border border-transparent bg-black text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
    <div
      className="inline-block h-5 w-5 animate-spin rounded-full border-2 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]"
      role="status">
    </div>
    <span class="ml-2">{{ text }}</span>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
  active: Boolean,
  text: String,
})
</script>

<style lang="scss" scoped></style>