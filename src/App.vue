<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import NavBar from '../components/navBar.vue'

const route = useRoute()

const showNavbar = computed(() => route.path !== '/login')
</script>

<template>
  <div>
    <NavBar v-if="showNavbar" />

    <main :class="showNavbar ? 'mt-5' : ' '">
      <router-view />
    </main>
  </div>
</template>

<style scoped></style>
