<script setup lang="ts">
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";

import { useNotificationStore } from "../stores/notification";

const email = ref("");
const password = ref("");
const authStore = useAuthStore();
const notificationStore = useNotificationStore();

const handleSubmit = async () => {
  try {
    await authStore.login({
      email: email.value,
      password: password.value
    });
    notificationStore.add("Login realizado com sucesso!", "success");
  } catch (error) {
    notificationStore.add(authStore.error || "Falha no login. Verifique suas credenciais.", "error");
  }
};

const fillCredentials = (e: string, p: string) => {
  email.value = e;
  password.value = p;
};
</script>

<template>
  <div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Entrar</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-mail:</label>
          <input
            type="email"
            id="email"
            v-model="email"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
        </div>
        <div class="mb-6">
          <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Senha:</label>
          <input
            type="password"
            id="password"
            v-model="password"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
        </div>
        <div class="flex items-center justify-between">
          <button
            type="submit"
            :disabled="authStore.loading"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ authStore.loading ? "Entrando..." : "Entrar" }}
          </button>
        </div>
        <div v-if="authStore.error" class="mt-4 text-center text-red-500 text-sm">
          {{ authStore.error }}
        </div>
      </form>

      <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-sm text-gray-500 text-center mb-4">Login Rápido (Clique para preencher)</p>
        <div class="grid grid-cols-1 gap-3">
          <div
            @click="fillCredentials('admin@example.com', 'password')"
            class="group cursor-pointer p-3 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-sm transition-all duration-200 border-l-4 border-l-indigo-500 bg-gray-50 hover:bg-white"
          >
            <div class="flex items-center justify-between">
              <div>
                <p
                  class="font-semibold text-sm text-gray-800 group-hover:text-indigo-600 transition-colors"
                >
                  Administrador
                </p>
                <p class="text-xs text-gray-500 mt-1">admin@example.com</p>
              </div>
              <span class="text-xs font-mono bg-gray-200 text-gray-600 px-2 py-1 rounded"
                >Senha: password</span
              >
            </div>
          </div>

          <div
            @click="fillCredentials('user@example.com', 'password')"
            class="group cursor-pointer p-3 border border-gray-200 rounded-lg hover:border-green-500 hover:shadow-sm transition-all duration-200 border-l-4 border-l-green-500 bg-gray-50 hover:bg-white"
          >
            <div class="flex items-center justify-between">
              <div>
                <p
                  class="font-semibold text-sm text-gray-800 group-hover:text-green-600 transition-colors"
                >
                  Usuário Comum
                </p>
                <p class="text-xs text-gray-500 mt-1">user@example.com</p>
              </div>
              <span class="text-xs font-mono bg-gray-200 text-gray-600 px-2 py-1 rounded"
                >Senha: password</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
