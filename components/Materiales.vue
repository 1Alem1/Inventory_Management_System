<script setup>
import { ref, onMounted, computed } from "vue";

const materiales = ref([]);
const filtro = ref("");

const modalMode = ref("add");

const form = ref({
  IDRepuesto: null,
  Nombre: "",
  Categoria: "",
  Descripcion: "",
  Stock: 0,
  Precio: 0,
  Imagen: ""
});

const formTecnico = ref({
  nombre: "",
  email: "",
  password: ""
});

const cargarMateriales = async () => {
  const res = await fetch("http://localhost/tpFinalProgra/backend/materiales.php");
  materiales.value = await res.json();
};

onMounted(cargarMateriales);

const filtrados = computed(() =>
  materiales.value.filter((m) =>
    m.Nombre.toLowerCase().includes(filtro.value.toLowerCase())
  )
);

function stockClass(stock) {
  if (stock == 0) return "text-danger fw-bold";

  else if (stock <= 5) return "text-warning fw-bold";

  else
  return "text-success fw-bold";
}

function abrirModalAgregar() {
  modalMode.value = "add";
  form.value = {
    IDRepuesto: null,
    Nombre: "",
    Categoria: "",
    Descripcion: "",
    Stock: 0,
    Precio: 0,
    Imagen: ""
  };

  new bootstrap.Modal(document.getElementById("materialModal")).show();
}

function abrirModalEditar(material) {
  modalMode.value = "edit";
  form.value = { ...material };
  new bootstrap.Modal(document.getElementById("materialModal")).show();
}

function abrirModalTecnico() {
  formTecnico.value = {
    nombre: "",
    email: "",
    password: ""
  };
  new bootstrap.Modal(document.getElementById("tecnicoModal")).show();
}

async function guardarMaterial() {
  const base = "http://localhost/tpFinalProgra/backend/materiales.php";

  const url = modalMode.value === "edit"
    ? `${base}?id=${form.value.IDRepuesto}`
    : base;

  const method = modalMode.value === "edit" ? "PUT" : "POST";

  const res = await fetch(url, {
    method,
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(form.value)
  });

  const text = await res.text();
  const data = JSON.parse(text);

  if (data.success) {
    await cargarMateriales();
    bootstrap.Modal.getInstance(document.getElementById("materialModal")).hide();
  } else {
    alert("Error al guardar");
  }
}

async function guardarTecnico() {
  const formData = new FormData();
  formData.append('nombre', formTecnico.value.nombre);
  formData.append('email', formTecnico.value.email);
  formData.append('password', formTecnico.value.password);

try {
  const res = await fetch("http://localhost/tpFinalProgra/backend/crearTecnico.php", {
    method: "POST",
    body: formData
  });

  const data = await res.json();
  
  if (data.success) {
    alert(data.message);
    bootstrap.Modal.getInstance(document.getElementById("tecnicoModal")).hide();
    formTecnico.value = { nombre: "", email: "", password: "" };
  } else {
    alert(data.error);
  }
} catch (error) {
  alert("Error de conexión al crear técnico");
  console.error(error);
}
}

async function eliminarMaterial(id) {
  const url = `http://localhost/tpFinalProgra/backend/materiales.php?id=${id}`;

  await fetch(url, { method: "DELETE" });
  await cargarMateriales();
}
</script>

<template>
  <div class="container mainContent">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Gestión de Materiales</h3>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" @click="abrirModalTecnico">
          <i class="bi bi-person-plus-fill me-2"></i> Agregar Técnico
        </button>
        
        <button class="btn btn-success" @click="abrirModalAgregar">
          <i class="bi bi-plus-square-fill me-2"></i> Agregar Material
        </button>
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
          <th>Imagen</th>
          <th style="width: 150px">Acciones</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="m in filtrados" :key="m.IDRepuesto">
          <td>{{ m.IDRepuesto }}</td>
          <td>{{ m.Nombre }}</td>
          <td>{{ m.Categoria }}</td>
          <td :class="stockClass(m.Stock)">{{ m.Stock }}</td>
          <td>${{ m.Precio }}</td>

          <td>
            <img v-if="m.Imagen" :src="m.Imagen" style="width:60px; height:auto; border-radius:5px;">
          </td>

          <td>
            <button class="btn btn-sm btn-warning me-2" @click="abrirModalEditar(m)">
              <i class="bi bi-pencil-square"></i>
            </button>

            <button class="btn btn-sm btn-danger" @click="eliminarMaterial(m.IDRepuesto)">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="modal fade" id="materialModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ modalMode === "add" ? "Agregar Material" : "Editar Material" }}
            </h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <label class="form-label">Nombre</label>
            <input class="form-control mb-2" v-model="form.Nombre" />

            <label class="form-label">Categoría</label>
            <input class="form-control mb-2" v-model="form.Categoria" />

            <label class="form-label">Descripción</label>
            <textarea class="form-control mb-2" v-model="form.Descripcion"></textarea>

            <label class="form-label">Stock</label>
            <input type="number" class="form-control mb-2" v-model="form.Stock" />

            <label class="form-label">Precio</label>
            <input type="number" class="form-control mb-2" v-model="form.Precio" />

            <label class="form-label mt-2">URL de la Imagen</label>
            <input
              type="text"
              class="form-control mb-2"
              placeholder="https://ejemplo.com/imagen.png"
              v-model="form.Imagen"
            />
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary" @click="guardarMaterial">
              Guardar
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="tecnicoModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-person-plus-fill me-2"></i> Agregar Nuevo Técnico
            </h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre Completo</label>
              <input 
                type="text" 
                class="form-control" 
                v-model="formTecnico.nombre"
                placeholder="Juan Pérez"
                required
              />
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input 
                type="email" 
                class="form-control" 
                v-model="formTecnico.email"
                placeholder="tecnico@ejemplo.com"
                required
              />
            </div>

            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input 
                type="password" 
                class="form-control" 
                v-model="formTecnico.password"
                placeholder="Mínimo 6 caracteres"
                required
              />
            </div>

            <div class="alert alert-info mb-0">
              <i class="bi bi-info-circle-fill me-2"></i>
              El técnico podrá acceder con estas credenciales.
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary" @click="guardarTecnico">
              <i class="bi bi-save-fill me-2"></i> Crear Técnico
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>