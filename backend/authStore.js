// stores/authStore.js
import { ref } from 'vue';

const isAuthenticated = ref(false);
const userRole = ref(null);
const userEmail = ref(null);
const isLoading = ref(true);

export function useAuthStore() {
  
  const checkSession = async () => {
    isLoading.value = true;
    try {
      const res = await fetch('http://localhost/tpFinalProgra/backend/checkSession.php', {
        credentials: 'include'
      });
      
      const data = await res.json();
      
      if (data.authenticated) {
        isAuthenticated.value = true;
        userRole.value = data.rol;
        userEmail.value = data.email;
      } else {
        clearSession();
      }
    } catch (err) {
      console.error('Error verificando sesión:', err);
      clearSession();
    } finally {
      isLoading.value = false;
    }
  };

  const login = (email, rol) => {
    isAuthenticated.value = true;
    userRole.value = rol;
    userEmail.value = email;
  };

  const logout = async () => {
    try {
      await fetch('http://localhost/tpFinalProgra/backend/cerrarSesion.php', {
        credentials: 'include'
      });
    } catch (err) {
      console.error('Error al cerrar sesión:', err);
    } finally {
      clearSession();
    }
  };

  const clearSession = () => {
    isAuthenticated.value = false;
    userRole.value = null;
    userEmail.value = null;
  };

  return {
    isAuthenticated,
    userRole,
    userEmail,
    isLoading,
    checkSession,
    login,
    logout
  };
}