<script setup lang="ts">
import { RouterLink } from 'vue-router'
import JobListing from './JobListing.vue'
import { ref, defineProps, onMounted } from 'vue'
// @ts-ignore
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
import axios from 'axios'

interface Job {
  id: number
  type: string
  title: string
  location: string
  description: string
  salary: string
  company: {
    name: string
    description: string
    contactEmail: string
    contactPhone: string
  }
}

defineProps({
  limit: Number,
  showButton: {
    type: Boolean,
    default: false,
  },
})

const state = ref<{ jobs: Job[]; isLoading: boolean }>({
  jobs: [],
  isLoading: true,
})

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/server'

onMounted(async () => {
  try {
    const response = await axios.get(`${API_URL}/jobs`)
    state.value.jobs = response.data
  } catch (error) {
    console.error('Error fetching jobs', error)
  } finally {
    state.value.isLoading = false
    console.log('Jobs fetched successfully')
  }
})
</script>

<template>
  <section class="bg-blue-50 px-4 py-10">
    <div class="container-xl lg:container m-auto">
      <h2 class="text-3xl font-bold text-green-500 mb-6 text-center">Browse Jobs</h2>
      <div v-if="state.isLoading" class="text-center text-gray-500 py-6">
        <PulseLoader />
      </div>
      <div v-else class="grid frid-cols-1 md:grid-cols-3 gap-6">
        <JobListing
          v-for="job in state.jobs.slice(0, limit || state.jobs.length)"
          :key="job.id"
          :job="job"
        />
      </div>
    </div>
  </section>

  <section v-if="showButton" class="m-auto max-w-lg my-10 px-6">
    <RouterLink
      to="/jobs"
      class="block bg-black text-white text-center py-4 px-6 rounded-xl hover:bg-gray-700"
      >View All Jobs</RouterLink
    >
  </section>
</template>
