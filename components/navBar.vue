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
          src="https://ss-static-001.esmsv.com/r/content/host1/1d1918010a2113f1c8d64b7544232b3f/img/Frame%203.png"
          alt="Logo"
        />
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link mx-2" to="/materiales">Materiales</router-link>
          </li>
<li class="nav-item">
  <router-link 
    class="nav-link mx-2" 
    :to="authStore.userRole && authStore.userRole.value === 'admin'
        ? '/pedidos'
        : '/pedidosTecnico'"
  >
    Pedidos
  </router-link>
</li>
<li class="nav-item" v-if="authStore.userRole && authStore.userRole.value === 'admin'">
  <router-link class="nav-link mx-2" to="/movimientos">Movimientos</router-link>
</li>
<li class="nav-item">
  <button v-if="authStore.isAuthenticated" @click="handleLogout" class="btn btn-danger ms-3">Cerrar sesión</button>
</li>
        </ul>

      </div>

    </div>
  </nav>
</template>