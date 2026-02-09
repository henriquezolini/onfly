<script setup lang="ts">
import { RouterView, RouterLink } from "vue-router";
import { useAuthStore } from "./stores/auth";
import ToastNotification from "./components/ToastNotification.vue";

const authStore = useAuthStore();
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <ToastNotification />
    <header v-if="authStore.isAuthenticated" class="bg-indigo-600 text-white shadow-md">
      <nav class="container mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <RouterLink to="/" class="text-xl font-bold hover:text-indigo-200 transition"
            >Solicitações de Viagem</RouterLink
          >
          <div class="hidden md:flex space-x-4">
            <RouterLink to="/" class="hover:text-indigo-200 transition">Painel</RouterLink>
            <RouterLink to="/create-request" class="hover:text-indigo-200 transition"
              >Nova Solicitação</RouterLink
            >
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <span v-if="authStore.user" class="text-sm">
            Olá, {{ authStore.user.name }} ({{ authStore.user.is_admin ? "Admin" : "Usuário" }})
          </span>
          <a
            href="#"
            @click.prevent="authStore.logout"
            class="bg-indigo-700 hover:bg-indigo-800 px-3 py-1 rounded transition text-sm"
            >Sair</a
          >
        </div>
      </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
      <RouterView />
    </main>
  </div>
</template>
