<script setup>
import { ref, onMounted } from "vue";

const movimientos = ref([]);
const loading = ref(false);
const error = ref(null);

async function cargarMovimientos() {
  loading.value = true;
  error.value = null;

  try {
    const resp = await fetch(
      "http://localhost/tpFinalProgra/backend/movimientos.php"
    );
    if (!resp.ok) {
      throw new Error("Respuesta no OK: " + resp.status);
    }

    const data = await resp.json();

    if (Array.isArray(data)) {
      movimientos.value = data;
    } else if (data && Array.isArray(data.data)) {
      movimientos.value = data.data;
    } else {
      error.value = "Respuesta inesperada del servidor";
    }
  } catch (err) {
    error.value = "Error al consultar movimientos";
    console.error(err);
  } finally {
    loading.value = false;
  }
}

function tipoLabel(tipo) {
  return Number(tipo) === 0 ? "Salida" : "Entrada";
}

function tipoClass(tipo) {
  return Number(tipo) === 0 ? "salida" : "entrada";
}

function formatFecha(fecha) {
  if (!fecha) return "";
  const d = new Date(fecha);
  if (isNaN(d)) return fecha;

  const day = String(d.getDate()).padStart(2, "0");
  const month = String(d.getMonth() + 1).padStart(2, "0");
  const year = d.getFullYear();

  return `${day}-${month}-${year}`;
}

function formatMonto(monto) {
  if (monto == null) return "";
  return (
    "$ " +
    Number(monto).toLocaleString(undefined, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
  );
}

const description = (m) => {
  return Number(m.Tipo) === 0 ? `Pedido #${m.IDPedido}` : "Carga de stock";
};

onMounted(() => {
  cargarMovimientos();
});
</script>

<template>
  <div class="movimientos">
    <h2 class="pb-5">Movimientos</h2>

    <div v-if="loading">Cargando...</div>
    <div v-else-if="error">{{ error }}</div>
    <table v-else class="table table-hover table-striped align-middle">
      <thead class="table-secondary">
        <tr class="py-2 px-3">
          <th class="py-2 px-3">Fecha</th>
          <th class="py-2 px-3">Usuario</th>
          <th class="py-2 px-3">Descripción</th>
          <th class="py-2 px-3">Monto</th>
          <th class="py-2 px-3">Tipo</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="m in movimientos" :key="m.IDMovimiento">
          <td class="py-2 px-3">{{ formatFecha(m.Fecha) }}</td>
          <td class="py-2 px-3">{{ m.Nombre }}</td>
          <td class="py-2 px-3">{{ description(m) }}</td>
          <td class="py-2 px-3">{{ m.Monto ? formatMonto(m.Monto) : "" }}</td>
          <td class="py-2 px-3">
            <span :class="tipoClass(m.Tipo)">
              {{ tipoLabel(m.Tipo) }}
            </span>
          </td>
        </tr>

        <tr v-if="movimientos.length === 0">
          <td colspan="4" class="vacío">No hay movimientos</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.salida {
  color: #c53030;
  font-weight: 600;
}
.entrada {
  color: #2d8f2d;
  font-weight: 600;
}
.vacío {
  text-align: center;
  color: #666;
}
</style>
