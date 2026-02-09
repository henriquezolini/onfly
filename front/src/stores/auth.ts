import { defineStore } from "pinia";
import axios from "axios";
import { ref, computed } from "vue";
import router from "../router";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(JSON.parse(localStorage.getItem("user") || "null"));
  const token = ref(localStorage.getItem("token"));
  const loading = ref(false);
  const error = ref("");

  const isAuthenticated = computed(() => !!token.value);
  const isAdmin = computed(() => user.value?.is_admin);

  if (token.value) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
  }
  axios.defaults.baseURL = "http://localhost:8000/api";

  async function login(credentials: any) {
    loading.value = true;
    error.value = "";
    try {
      await axios.get("/sanctum/csrf-cookie", { baseURL: "http://localhost:8000" });
      const response = await axios.post("/login", credentials);

      token.value = response.data.access_token;
      user.value = response.data.user;

      localStorage.setItem("token", token.value!);
      localStorage.setItem("user", JSON.stringify(user.value));

      axios.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;

      router.push({ name: "dashboard" });
    } catch (e: any) {
      if (e.response && e.response.data && e.response.data.errors) {
        error.value = Object.values(e.response.data.errors).flat().join(", ");
      } else {
        error.value = "Falha no login. Verifique suas credenciais.";
      }
      throw e;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      await axios.post("/logout");
    } catch (e) {
      console.error("Logout error", e);
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      delete axios.defaults.headers.common["Authorization"];
      router.push({ name: "login" });
    }
  }

  return { user, token, isAuthenticated, isAdmin, login, logout, loading, error };
});
