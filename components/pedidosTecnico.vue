<script setup>
import { ref, onMounted, computed } from "vue";

const pedidos = ref([]);
const selectedPedido = ref(null);
const pedidoItems = ref([]);

const showModal = ref(false);

async function loadPedidos() {
  const res = await fetch(
    "http://localhost/tpFinalProgra/backend/pedidos.php",
    {
      credentials: "include",
    }
  );
  pedidos.value = await res.json();
}

async function verPedido(idPedido) {
  const res = await fetch(
    `http://localhost/tpFinalProgra/backend/pedidos.php?id=${idPedido}`,
    { credentials: "include" }
  );

  if (!res.ok) {
    console.error("Error:", res.status);
    return;
  }

  const data = await res.json();
  pedidoItems.value = data.items ?? [];

  selectedPedido.value = pedidos.value.find((p) => p.IDPedido === idPedido);

  const modal = new bootstrap.Modal(document.getElementById("modalPedido"));
  modal.show();
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

function badgeClass() {
  if (!selectedPedido.value) return "";
  switch (selectedPedido.value.Estado) {
    case "Aprobado":
      return "bg-success";
    case "Rechazado":
      return "bg-danger";
    default:
      return "bg-warning";
  }


}

const totalPedido = computed(() => {
  return pedidoItems.value.reduce((sum, item) => sum + parseFloat(item.Monto || 0), 0);
});

onMounted(loadPedidos);
</script>
<template>
  <div class="container mt-4">
    <h2 class="mb-4">Historial de Pedidos</h2>

    <div>
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th class="py-2 px-3">ID</th>
            <th class="py-2 px-3">Fecha</th>
            <th class="py-2 px-3">Estado</th>
            <th class="py-2 px-3">Total Items</th>
            <th class="py-2 px-3">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in pedidos" :key="p.IDPedido">
            <td class="py-2 px-3">#{{ p.IDPedido }}</td>
            <td class="py-2 px-3">{{ p.Fecha }}</td>
            <td class="py-2 px-3">
              <span :class="estadoClass(p.Estado)">{{ p.Estado }}</span>
            </td>
            <td class="py-2 px-3">{{ p.totalItems }}</td>
            <td class="py-2 px-3">
              <button
                class="btn btn-primary btn-sm"
                @click="verPedido(p.IDPedido)"
              >
                <i class="bi bi-eye"></i> Ver detalles
              </button>
            </td>
          </tr>

          <tr v-if="pedidos.length === 0">
            <td colspan="5" class="text-center text-muted py-4">
              No hay pedidos registrados
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="modal fade" id="modalPedido" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pedido #{{ selectedPedido?.IDPedido }}</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>

          <div class="modal-body">
            <p><strong>Fecha:</strong> {{ selectedPedido?.Fecha }}</p>
            <p>
              <strong>Estado:</strong>
              <span :class="['badge', badgeClass(), 'ms-1']">{{selectedPedido?.Estado}}</span>
            </p>

            <hr />

            <h6>Items</h6>

            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Material</th>
                  <th>Categoría</th>
                  <th>Cantidad</th>
                  <th>Precio unitario</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="it in pedidoItems" :key="it.IDItem">
                  <td>{{ it.Nombre }}</td>
                  <td>{{ it.Categoria }}</td>
                  <td>{{ it.Cantidad }}</td>
                  <td>${{ Number(it.Precio).toFixed(2) }}</td>
                  <td>${{ Number(it.Monto).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>

            <div class="d-flex justify-content-end mt-3 pt-2">
              <h5>Total del pedido: ${{ totalPedido.toFixed(2) }}</h5>
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.estado-aprobado {
  color: #2d8f2d;
  font-weight: bold;
}
.estado-rechazado {
  color: #c53030;
  font-weight: bold;
}
</style>
