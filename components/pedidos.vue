<script setup>
import { ref, onMounted } from "vue";

const pedidos = ref([]);
const loading = ref(false);
const error = ref(null);
const processingIds = ref(new Set());

const API_URL = "http://localhost/tpFinalProgra/backend/pedidos.php";

async function fetchPedidos() {
  loading.value = true;
  error.value = null;
  try {
    const res = await fetch(API_URL, { credentials: "include" });
    if (!res.ok) throw new Error("Error HTTP " + res.status);
    const data = await res.json();
    pedidos.value = Array.isArray(data) ? data : [];
  } catch (err) {
    console.error(err);
    error.value = "No se pudieron cargar los pedidos.";
  } finally {
    loading.value = false;
  }
}

async function handleAction(id, nuevoEstado) {
  if (!confirm(`¿Desea ${nuevoEstado.toLowerCase()} el pedido ${id}?`)) return;

  processingIds.value.add(id);
  try {
    const res = await fetch(API_URL, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({
        idPedido: id,
        estado: nuevoEstado,
      }),
    });
    if (!res.ok) throw new Error("Error en la solicitud");
    const json = await res.json();
    if (json.success) {
      const idx = pedidos.value.findIndex((p) => p.IDPedido === id);
      if (idx !== -1) pedidos.value[idx].Estado = nuevoEstado;
    } else {
      alert(json.error || json.message || "La acción falló en el servidor.");
    }
  } catch (err) {
    console.error(err);
    alert("Error al procesar la acción.");
  } finally {
    processingIds.value.delete(id);
  }
}

function formatDate(v) {
  if (!v) return "";
  const d = new Date(v);
  return isNaN(d) ? v : d.toLocaleString();
}

function estadoClass(estado) {
  switch (estado) {
    case "Aprobado":
      return "estado-aprobado";
    case "Rechazado":
      return "estado-rechazado";
    default:
      return "";
  }
}

onMounted(fetchPedidos);
</script>

<template>
  <div class="pedidos">
    <h2 class="pb-5">Lista de pedidos</h2>

    <div v-if="loading">Cargando pedidos...</div>
    <div v-if="error">{{ error }}</div>

    <table v-if="!loading && pedidos.length" class="table table-hover align-middle table-bordered">
      <thead class="thColor">
        <tr>
          <th class="py-3 px-3">IDPedido</th>
          <th class="py-3 px-3">Solicitante</th>
          <th class="py-3 px-3">Fecha</th>
          <th class="py-3 px-3">Estado</th>
          <th class="py-3 px-3">Productos</th>
          <th class="py-3 px-3">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in pedidos" :key="p.IDPedido">
          <td class="py-2 px-3">{{ p.IDPedido }}</td>
          <td class="py-2 px-3">{{ p.Nombre }}</td>
          <td class="py-2 px-3">{{ formatDate(p.Fecha) }}</td>
          <td :class="estadoClass(p.Estado)" class="py-2 px-3">{{ p.Estado }}</td>
          <td class="py-2 px-3">
            <ul class="mb-0 ps-3">
              <li v-for="item in p.items" :key="item.IDItem">
                {{ item.Nombre }} x {{ item.Cantidad }}
              </li>
            </ul>
          </td>
          <td class="py-2 px-3">
            <button
              class="btn btn-success"
              :disabled="
                processingIds.has(p.IDPedido) || p.Estado !== 'Pendiente'
              "
              @click="handleAction(p.IDPedido, 'Aprobado')"
            >
              Aprobar
            </button>

            <button
              class="btn btn-danger"
              :disabled="
                processingIds.has(p.IDPedido) || p.Estado !== 'Pendiente'
              "
              @click="handleAction(p.IDPedido, 'Rechazado')"
            >
              Rechazar
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!loading && !pedidos.length" class="info">No hay pedidos.</div>
  </div>
</template>



<style scoped>

.btn {
  padding: 0.35rem 0.6rem;
  margin-right: 1rem;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}
.estado-aprobado {
  color: #2d8f2d;
  font-weight: bold;
}
.estado-rechazado {
  color: #c53030;
  font-weight: bold;
}
</style>
