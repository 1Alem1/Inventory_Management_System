<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import NavBar from '../components/navBar.vue'
import { useAuthStore } from '../backend/authStore.js';

const authStore = useAuthStore();

onMounted(async () => {
  await authStore.checkSession();
});

const route = useRoute()
const showNavbar = computed(() => route.path !== '/login')
</script>

<template>
  <div>
    <NavBar
      v-if="showNavbar"
    />

    <main :class="showNavbar ? 'mt-5' : ''">
      <router-view />
    </main>
  </div>
</template>

<style scoped></style>
