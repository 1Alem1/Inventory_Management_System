<script setup>
import { ref, onMounted, computed } from "vue";

const materiales = ref([]);
const filtro = ref("");

const cargarMateriales = async () => {
  const res = await fetch("http://localhost/tpFinalProgra/backend/materiales.php");
  materiales.value = await res.json();
};

onMounted(() => cargarMateriales());

const filtrados = computed(() =>
  materiales.value.filter(m =>
    m.Nombre.toLowerCase().includes(filtro.value.toLowerCase())
  )
);

function stock(stock) {
  if (stock === 0) return "text-danger fw-bold";
  if (stock < 5) return "text-warning fw-bold";
  return "text-success fw-bold";
}
</script>

<template>
<div class="container mt-5 pt-5">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Gestión de Materiales</h3>
    <button class="btn btn-success me-2 d-flex align-items-center">
        <i class="bi bi-plus-square-fill me-2"></i>
        Agregar material
    </button>
  </div>

  <input
    v-model="filtro"
    class="form-control mb-3"
    placeholder="Buscar por nombre..."
  />

  <table class="table table-hover align-middle">
    <thead class="thColor">
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Stock</th>
        <th>Precio</th>
        <th style="width: 140px;">Acciones</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="m in filtrados" :key="m.IDRepuesto">
        <td>{{ m.IDRepuesto }}</td>
        <td>{{ m.Nombre }}</td>
        <td>{{ m.Categoria }}</td>
        <td :class="stock(m.Stock)">{{ m.Stock }}</td>
        <td>${{ m.Precio }}</td>

        <td>
          <button class="btn btn-sm btn-warning me-2">
            <i class="bi bi-pencil-square"></i>
          </button>
          <button class="btn btn-sm btn-danger me-2">
            <i class="bi bi-trash3-fill"></i>
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

</template>
