<script setup>
import { useRoute } from "vue-router";
import { useAuthStore } from "../backend/authStore";

const route = useRoute();
const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
};
</script>

<template>
  <nav class="navbar navbar-expand-md backColor sticky-top">
    <div class="container-fluid">

      <a class="navbar-brand" href="#">
        <img
          src="/icon.ico"
          alt="Logo"
        />
      </a>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">

          <!-- MATERIALES -->
          <li :class="{ active: route.path.startsWith('/materiales') }" class="nav-item">
  <router-link class="nav-link mx-2" to="/materiales">
    Materiales
  </router-link>
</li>

          <!-- PEDIDOS -->
          <li
            :class="{
              active:
                route.path === '/pedidos' ||
                route.path === '/pedidosTecnico'
            }"
            class="nav-item"
          >
            <router-link
              class="nav-link mx-2"
              :to="
                authStore.userRole?.value === 'admin'
                  ? '/pedidos'
                  : '/pedidosTecnico'
              "
            >
              Pedidos
            </router-link>
          </li>

          <!-- MOVIMIENTOS SOLO ADMIN -->
          <li
            v-if="authStore.userRole?.value === 'admin'"
            :class="{ active: route.path === '/movimientos' }"
            class="nav-item"
          >
            <router-link class="nav-link mx-2" to="/movimientos">
              Movimientos
            </router-link>
          </li>

          <!-- LOGOUT -->
          <li class="nav-item">
            <button
              v-if="authStore.isAuthenticated"
              @click="handleLogout"
              class="btn btn-danger ms-3"
            >
              Cerrar sesión
            </button>
          </li>
        </ul>
      </div>

    </div>
  </nav>
</template>

<style>
.active > a {
  background-color: #28a745 !important; /* Verde */
  color: white !important;
  border-radius: 6px;
}
</style>