<script setup>

import { useRouter } from "vue-router";
import { useAuthStore } from '../backend/authStore.js';



const router = useRouter();
const authStore = useAuthStore();

async function handleLogout() {
  await authStore.logout();
  router.push('/login');
}


defineProps({
  logged: Boolean,
  rol: String,
  cerrarSesion: Function,
})

</script>



<template>
  <nav class="navbar navbar-expand-md backColor sticky-top">
    <div class="container-fluid">

      <a class="navbar-brand" href="#">
        <img
          src="../public/logo.png"
          alt="Logo"
        />
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
<router-link 
  class="nav-link mx-2"
  active-class="router-link-exact-active"
  :to="authStore.userRole?.value === 'admin'
        ? '/materiales'
        : '/materialesTecnico'"
>
  Materiales
</router-link>
<li class="nav-item">
  <router-link 
    class="nav-link mx-2" active-class="router-link-exact-active"
    :to="authStore.userRole && authStore.userRole.value === 'admin'
        ? '/pedidos'
        : '/pedidosTecnico'"
  >
    Pedidos
  </router-link>
</li>
<li class="nav-item" v-if="authStore.userRole && authStore.userRole.value === 'admin'">
  <router-link class="nav-link mx-2" to="/movimientos" active-class="router-link-exact-active">Movimientos</router-link>
</li>
<li class="nav-item">
  <button v-if="authStore.isAuthenticated" @click="handleLogout" class="btn btn-danger ms-3">Cerrar sesión</button>
</li>
        </ul>

      </div>

    </div>
  </nav>
</template>