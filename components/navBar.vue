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
            <router-link class="nav-link mx-2" to="/">Inicio</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link mx-2" to="/materiales">Materiales</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link mx-2" to="/pedidos">Pedidos</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link mx-2" to="/movimientos">Movimientos</router-link>
          </li>
        </ul>
      </div>

    </div>
  </nav>
</template>
