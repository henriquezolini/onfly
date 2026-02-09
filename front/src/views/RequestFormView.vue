<script setup lang="ts">
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useNotificationStore } from "../stores/notification";

const destination = ref("");
const departure_date = ref("");
const return_date = ref("");
const router = useRouter();
const error = ref("");
const loading = ref(false);
const notificationStore = useNotificationStore();

const handleSubmit = async () => {
  loading.value = true;
  error.value = "";
  try {
    await axios.post("/travel-requests", {
      destination: destination.value,
      departure_date: departure_date.value,
      return_date: return_date.value
    });
    notificationStore.add("Solicitação de viagem criada com sucesso!", "success");
    router.push("/");
  } catch (e: any) {
    if (e.response && e.response.data && e.response.data.errors) {
      error.value = Object.values(e.response.data.errors).flat().join(", ");
    } else {
      error.value = "Falha ao criar solicitação.";
    }
    notificationStore.add(error.value, "error");
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Nova Solicitação de Viagem</h1>
    <form @submit.prevent="handleSubmit">
      <div class="mb-4">
        <label for="destination" class="block text-gray-700 text-sm font-bold mb-2">Destino:</label>
        <input
          type="text"
          id="destination"
          v-model="destination"
          required
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        />
      </div>
      <div class="mb-4">
        <label for="departure_date" class="block text-gray-700 text-sm font-bold mb-2"
          >Data de Ida:</label
        >
        <input
          type="date"
          id="departure_date"
          v-model="departure_date"
          required
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        />
      </div>
      <div class="mb-6">
        <label for="return_date" class="block text-gray-700 text-sm font-bold mb-2"
          >Data de Volta:</label
        >
        <input
          type="date"
          id="return_date"
          v-model="return_date"
          required
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        />
      </div>
      <div class="flex items-center justify-end">
        <button
          type="submit"
          :disabled="loading"
          class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? "Enviando..." : "Criar Solicitação" }}
        </button>
      </div>
      <div
        v-if="error"
        class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded relative"
        role="alert"
      >
        <span class="block sm:inline">{{ error }}</span>
      </div>
    </form>
  </div>
</template>
