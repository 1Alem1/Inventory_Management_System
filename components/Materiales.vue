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
  if (stock <= 5) return "text-warning fw-bold";
  return "text-success fw-bold";
}
</script>

<template>
<div class="container mainContent">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Gestión de Materiales</h3>
    <button class="btn btn-success me-2 d-flex align-items-center">
        <i class="bi bi-plus-square-fill me-2"></i>
        Agregar material
    </button>
  </div>

<div class="pb-3">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#entregaModal">
        Cargar tecnico
    </button>
</div>

  <div class="modal fade" id="entregaModal" tabindex="-1" aria-labelledby="entregaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entregaModalLabel">Completá los datos!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
                <form method="POST" action="http://localhost/tpFinalProgra/backend/crearTecnico.php">

                <div class="modal-body">
                        <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
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
