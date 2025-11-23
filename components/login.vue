<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from '../backend/authStore.js';


const router = useRouter();
const authStore = useAuthStore();

const email = ref("");
const password = ref("");
const captcha = ref("");
const error = ref("");





async function login() {
  error.value = "";

  const form = new FormData();
  form.append("accion", "login");
  form.append("email", email.value);
  form.append("password", password.value);
  form.append("captcha", captcha.value);

  try{
    const res = await fetch("http://localhost/tpFinalProgra/backend/login.php", {
      method: "POST",
      body: form,
      credentials: "include",
    });
    const txt = await res.text();

if (txt.startsWith("loguser")) {

    const [, rol] = txt.split("|");

    authStore.login(email.value, rol);


    if (rol === "admin") {
      router.push("/materiales");
      return;
    }

    if (rol === "tecnico") {
      router.push("/materialesTecnico");
      return;
    }

    router.push("/login");
    return;
  }
    error.value = txt;
    captcha.value = "";
    reloadCaptcha();
    }
   catch (err) {
    error.value = "Error. Por favor, inténtelo de nuevo.";
    return;
  }
}


function reloadCaptcha() {
  const captchaImage = document.getElementById("captcha");
  captchaImage.src = "http://localhost/tpFinalProgra/backend/captcha.php?" + new Date().getTime();
}
  
</script>


<template>
  <div class="login-bg d-flex justify-content-center align-items-center vh-100">
    <main class="login-card p-5 shadow">

      <div class="logo-container mb-4">
        <img
          src="https://ss-static-001.esmsv.com/r/content/host1/1d1918010a2113f1c8d64b7544232b3f/img/Frame%203.png"
          alt="Logo"
          class="company-logo"
        />
      </div>

      <h2 class="mb-4 text-center fw-bold text-primary-custom">Inicio de sesión</h2>

      <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>

      <div class="form-floating mb-3">
        <input type="email" class="form-control" v-model="email" name="email" placeholder="Email">
        <label>Email</label>
      </div>

      <div class="form-floating mb-3">
        <input type="password" class="form-control" name="password" v-model="password" placeholder="Contraseña">
        <label>Contraseña</label>
      </div>
    
      <div class=" d-flex align-items-center mb-3">
        <img src="http://localhost/tpFinalProgra/backend/captcha.php" id="captcha"class="captcha" alt="CAPTCHA">
      <button @click="reloadCaptcha()" class="btn btn-secondary ms-2 btnReload"><i class="bi bi-arrow-clockwise"></i></button>
      </div>

      <div class="form-floating mb-3">
        <input type="text" class="form-control" name="captcha" v-model="captcha" placeholder="Captcha">
        <label>Captcha</label>
      </div>

      <button class="btn btn-primary-custom w-100 py-2 mt-2" @click="login">
        Iniciar sesión
      </button>

    </main>
  </div>
</template>


