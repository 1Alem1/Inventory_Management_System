<script setup>
import { ref, onMounted } from "vue";

const movimientos = ref([]);
const loading = ref(false);
const error = ref(null);
const pedidoDetalle = ref(null);
const loadingDetalle = ref(false);

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

async function verDetallePedido(idPedido) {
  if (!idPedido) return;
  
  loadingDetalle.value = true;
  pedidoDetalle.value = null;
  
  try {
    const resp = await fetch(
      `http://localhost/tpFinalProgra/backend/pedidos.php?id=${idPedido}`,
      {
        credentials: 'include'
      }
    );
    
    if (!resp.ok) {
      throw new Error("Error al cargar el pedido");
    }
    
    const data = await resp.json();
    pedidoDetalle.value = data;
  } catch (err) {
    console.error("Error al cargar detalle del pedido:", err);
    alert("Error al cargar el detalle del pedido");
  } finally {
    loadingDetalle.value = false;
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
          <td class="py-2 px-3">
            <span 
              v-if="m.IDPedido" 
              @click="verDetallePedido(m.IDPedido)"
              class="pedido-link"
              data-bs-toggle="modal" 
              data-bs-target="#modalDetallePedido"
            >
              {{ description(m) }}
            </span>
            <span v-else>{{ description(m) }}</span>
          </td>
          <td class="py-2 px-3">{{ m.Monto ? formatMonto(m.Monto) : "" }}</td>
          <td class="py-2 px-3">
            <span :class="tipoClass(m.Tipo)">
              {{ tipoLabel(m.Tipo) }}
            </span>
          </td>
        </tr>

        <tr v-if="movimientos.length === 0">
          <td colspan="5 ">
            <p class="text-center">No hay movimientos
            </p></td>
        </tr>
      </tbody>
    </table>

    <div class="modal fade" id="modalDetallePedido" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Detalle del Pedido #{{ pedidoDetalle?.IDPedido }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <div class="modal-body">
            <div v-if="loadingDetalle" class="text-center">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>
            
            <div v-else-if="pedidoDetalle">
              <div class="alert alert-light">
                <p class="mb-1"><strong>Usuario:</strong> {{ pedidoDetalle.Nombre }}</p>
                <p class="mb-1"><strong>Fecha:</strong> {{ pedidoDetalle.Fecha }}</p>
                <p class="mb-0">
                  <strong>Estado: </strong> 
                  <span :class="'badge bg-' + (pedidoDetalle.Estado === 'Pendiente' ? 'warning text-dark' : pedidoDetalle.Estado === 'Aprobado' ? 'success' : 'danger')">
                    {{ pedidoDetalle.Estado }}
                  </span>
                </p>
              </div>
              
              <h6 class="mt-3 mb-2">Items del pedido:</h6>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Repuesto</th>
                      <th>Categoría</th>
                      <th class="text-center">Cantidad</th>
                      <th class="text-end">Precio Unit.</th>
                      <th class="text-end">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in pedidoDetalle.items" :key="item.IDItem">
                      <td>{{ item.Nombre }}</td>
                      <td>{{ item.Categoria }}</td>
                      <td class="text-center">{{ item.Cantidad }}</td>
                      <td class="text-end">{{ formatMonto(item.Precio) }}</td>
                      <td class="text-end"><strong>{{ formatMonto(item.Monto) }}</strong></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr class="table-secondary">
                      <td colspan="4" class="text-end"><strong>Total:</strong></td>
                      <td class="text-end">
                        <strong>
                          {{ formatMonto(pedidoDetalle.items.reduce((sum, i) => sum + Number(i.Monto), 0)) }}
                        </strong>
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
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

</style>