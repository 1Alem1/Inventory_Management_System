<template>
  <div class="container mt-5">
    <h2 class="mb-4">Available Materials</h2>

    <input
      v-model="search"
      class="form-control mb-3"
      placeholder="Search materials..."
    />

    <div class="row g-4">
      <div
        v-for="item in filteredMaterials"
        :key="item.IDRepuesto"
        class="col-12 col-md-6 col-lg-4"
      >
        <div class="card h-100 shadow-sm">
          <img
            v-if="item.Imagen"
            :src="item.Imagen"
            class="card-img-top"
            alt="Material image"
            style="object-fit: cover; height: 180px;"
          />

          <div class="card-body">
            <h5 class="card-title">{{ item.Nombre }}</h5>
            <p class="text-muted">{{ item.Categoria }}</p>
            <p class="mb-1">
              <strong>Stock:</strong>
              <span :class="stockClass(item.Stock)">
                {{ item.Stock }}
              </span>
            </p>

            <p v-if="item.Descripcion" class="small text-secondary">
              {{ item.Descripcion }}
            </p>

            <button class="btn btn-primary w-100">
              View Material
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      materials: [],
      search: ""
    };
  },

  computed: {
    filteredMaterials() {
      return this.materials.filter(m =>
        m.Nombre.toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },

  methods: {
    stockClass(stock) {
      if (stock === 0) return "text-danger fw-bold";
      if (stock < 5) return "text-warning fw-bold";
      return "text-success fw-bold";
    },

    async loadMaterials() {
      const res = await fetch("http://localhost/tpFinalProgra/backend/materiales.php");
      this.materials = await res.json();
    }
  },

  mounted() {
    this.loadMaterials();
  }
};
</script>
