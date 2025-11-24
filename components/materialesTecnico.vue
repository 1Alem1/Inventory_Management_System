<script setup>
import { ref, computed, onMounted } from "vue";

const materials = ref([]);
const search = ref("");
const cart = ref([]);
const selectedCategory = ref("");

const filteredMaterials = computed(() => {
  return materials.value.filter(m => {
    const matchSearch = m.Nombre.toLowerCase().includes(search.value.toLowerCase());
    const matchCategory = !selectedCategory.value || m.Categoria === selectedCategory.value;
    return matchSearch && matchCategory;
  });
});

const categories = computed(() => {
  const cats = [...new Set(materials.value.map(m => m.Categoria))];
  return cats.filter(c => c);
});

const cartTotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.cantidad, 0);
});

function stockClass(stock) {
  if (stock === 0) return "text-danger fw-bold";
  if (stock <= 5) return "text-warning fw-bold";
  return "text-success fw-bold";
}

function addToCart(material) {
  const existingItem = cart.value.find(item => item.IDRepuesto === material.IDRepuesto);
  
  if (existingItem) {
    if (existingItem.cantidad < material.Stock) {
      existingItem.cantidad++;
    } else {
      alert(`No hay suficiente stock disponible (máximo: ${material.Stock})`);
    }
  } else {
    if (material.Stock > 0) {
      cart.value.push({
        IDRepuesto: material.IDRepuesto,
        Nombre: material.Nombre,
        Categoria: material.Categoria,
        Precio: material.Precio,
        StockDisponible: material.Stock,
        cantidad: 1
      });
    } else {
      alert("Este material no tiene stock disponible");
    }
  }
}
function removeFromCart(index) {
  cart.value.splice(index, 1);
}

function updateQuantity(item, newQuantity) {
  if (newQuantity > 0 && newQuantity <= item.StockDisponible) {
    item.cantidad = newQuantity;
  } else if (newQuantity > item.StockDisponible) {
    alert(`No hay suficiente stock disponible (máximo: ${item.StockDisponible})`);
    item.cantidad = item.StockDisponible;
  }
}
async function submitOrder() {
  if (cart.value.length === 0) {
    alert("El carrito está vacío");
    return;
  }

  const confirmed = confirm(`¿Confirmar pedido de ${cartTotal.value} items?`);
  
  if (!confirmed) return;
  
  try {
const res = await fetch("http://localhost/tpFinalProgra/backend/pedidos.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  credentials: "include",
  body: JSON.stringify({
    items: cart.value
  })
});

const text = await res.text();
let data;
try {
  data = JSON.parse(text);
} catch(err) {
  console.error("JSON inválido:", text);
  return;
}

if(data.success){
  alert(data.message);
  cart.value = [];
} else {
  alert("Error: " + (data.error || "No se pudo crear el pedido"));
}
  } catch (error) {
    console.error("Error al enviar el pedido:", error);
    alert("Error al enviar el pedido");
  }
}

function clearCart() {
  if (confirm("¿Estás seguro de que quieres vaciar el carrito?")) {
    cart.value = [];
  }
}

function isInCart(materialId) {
  return cart.value.some(item => item.IDRepuesto === materialId);
}

function getCartQuantity(materialId) {
  const item = cart.value.find(item => item.IDRepuesto === materialId);
  return item ? item.cantidad : 0;
}

async function loadMaterials() {
  const res = await fetch("http://localhost/tpFinalProgra/backend/materiales.php");
  materials.value = await res.json();
}

onMounted(() => loadMaterials());
</script>

<template>
  <div class="container-fluid mt-4">
    <div class="row">
      <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="mb-0">Materiales Disponibles</h2>
          <span class="badge bg-primary fs-6">
            {{ filteredMaterials.length }} materiales
          </span>
        </div>

        <div class="row mb-3">
          <div class="col-md-8">
            <input
              v-model="search"
              class="form-control"
              placeholder="Buscar materiales..."
            />
          </div>
          <div class="col-md-4">
            <select v-model="selectedCategory" class="form-select">
              <option value="">Todas las categorías</option>
              <option v-for="cat in categories" :key="cat" :value="cat">
                {{ cat }}
              </option>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Material</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Precio</th>
                <th style="width: 180px;">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in filteredMaterials" :key="item.IDRepuesto">
                <td>
                  <div class="d-flex align-items-center">
                    <img
                      v-if="item.Imagen"
                      :src="item.Imagen"
                      alt="Material"
                      class="me-2"
                      style="width: 40px; height: 40px; object-fit: contain;"
                    />
                    <div>
                      <strong>{{ item.Nombre }}</strong>
                      <br>
                      <small v-if="item.Descripcion" class="text-muted">
                        {{ item.Descripcion.substring(0, 50) }}{{ item.Descripcion.length > 50 ? '...' : '' }}
                      </small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-secondary">{{ item.Categoria }}</span>
                </td>
                <td>
                  <span :class="stockClass(item.Stock)">
                    {{ item.Stock }} unidades
                  </span>
                </td>
                <td>
                  <strong>${{ item.Precio }}</strong>
                </td>
                <td>
                  <button
                    v-if="!isInCart(item.IDRepuesto)"
                    @click="addToCart(item)"
                    class="btn btn-sm btn-primary"
                    :disabled="item.Stock === 0"
                  >
                    <i class="bi bi-cart-plus"></i>
                    Agregar
                  </button>
                  <span v-else class="badge bg-success">
                    <i class="bi bi-check-circle"></i>
                    En carrito ({{ getCartQuantity(item.IDRepuesto) }})
                  </span>
                </td>
              </tr>
              <tr v-if="filteredMaterials.length === 0">
                <td colspan="5" class="text-center text-muted py-4">
                  No se encontraron materiales
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card cart-sticky">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
              <i class="bi bi-cart3"></i> Mi Pedido
            </h5>
            <span class="badge bg-light text-primary">
              {{ cartTotal }} items
            </span>
          </div>

          <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            <div v-if="cart.length === 0" class="text-center text-muted py-5">
              <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
              <p class="mt-3">El carrito está vacío</p>
              <small>Agrega materiales para crear un pedido</small>
            </div>

            <div v-else>
              <div
                v-for="(item, index) in cart"
                :key="item.IDRepuesto"
                class="mb-3 pb-3 border-bottom"
              >
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="flex-grow-1">
                    <strong>{{ item.Nombre }}</strong>
                    <br>
                    <small class="text-muted">{{ item.Categoria }}</small>
                  </div>
                  <button
                    @click="removeFromCart(index)"
                    class="btn btn-sm btn-outline-danger"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                  <div class="input-group input-group-sm" style="width: 120px;">
                    <button
                      @click="updateQuantity(item, item.cantidad - 1)"
                      class="btn btn-outline-secondary"
                      :disabled="item.cantidad <= 1"
                    >
                      -
                    </button>
                    <input
                      type="number"
                      class="form-control text-center"
                      v-model.number="item.cantidad"
                      @change="updateQuantity(item, item.cantidad)"
                      min="1"
                      :max="item.StockDisponible"
                    />
                    <button
                      @click="updateQuantity(item, item.cantidad + 1)"
                      class="btn btn-outline-secondary"
                      :disabled="item.cantidad >= item.StockDisponible"
                    >
                      +
                    </button>
                  </div>
                  <strong>${{ (item.Precio * item.cantidad).toFixed(2) }}</strong>
                </div>
                <small class="text-muted">
                  Disponible: {{ item.StockDisponible }}
                </small>
              </div>
            </div>
          </div>

          <div v-if="cart.length > 0" class="card-footer">
            <div class="d-grid gap-2">
              <button @click="submitOrder" class="btn btn-success">
                <i class="bi bi-send"></i>
                Enviar Pedido
              </button>
              <button @click="clearCart" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-trash"></i>
                Vaciar Carrito
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>