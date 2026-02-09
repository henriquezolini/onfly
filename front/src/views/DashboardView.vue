<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";
import { useAuthStore } from "../stores/auth";
import { formatDate } from "../utils/date";
import { useNotificationStore } from "../stores/notification";
import ConfirmModal from "../components/ConfirmModal.vue";

const requests = ref<any[]>([]);
const loading = ref(true);
const filterStatus = ref("");
const authStore = useAuthStore();
const notificationStore = useNotificationStore();

const statusMap: Record<string, string> = {
  requested: "Solicitado",
  approved: "Aprovado",
  cancelled: "Cancelado"
};

// Modal state
const isModalOpen = ref(false);
const modalTitle = ref("");
const modalMessage = ref("");
const modalType = ref<"primary" | "danger">("primary");
const pendingAction = ref<{ id: number; status: string } | null>(null);

const fetchRequests = async () => {
  loading.value = true;
  try {
    const params: any = {};
    if (filterStatus.value) params.status = filterStatus.value;

    const response = await axios.get("/travel-requests", { params });
    requests.value = response.data;
  } catch (e) {
    console.error(e);
    notificationStore.add("Falha ao buscar solicitações", "error");
  } finally {
    loading.value = false;
  }
};

const openConfirmModal = (id: number, status: string) => {
  pendingAction.value = { id, status };
  const actionText = status === "approved" ? "aprovar" : "cancelar";
  modalTitle.value = status === "approved" ? "Aprovar Solicitação" : "Cancelar Solicitação";
  modalMessage.value = `Tem certeza que deseja ${actionText} esta solicitação? Esta ação não pode ser desfeita.`;
  modalType.value = status === "approved" ? "primary" : "danger";
  isModalOpen.value = true;
};

const closeConfirmModal = () => {
  isModalOpen.value = false;
  pendingAction.value = null;
};

const confirmAction = async () => {
  if (!pendingAction.value) return;

  const { id, status } = pendingAction.value;
  closeConfirmModal();

  try {
    await axios.patch(`/travel-requests/${id}/status`, { status });
    // Refresh list locally or fetch again
    const req = requests.value.find((r) => r.id === id);
    if (req) req.status = status;

    const action = status === "approved" ? "aprovada" : "cancelada";
    notificationStore.add(`Solicitação ${action} com sucesso`, "success");
  } catch (e: any) {
    const errorMessage = e.response?.data?.message || e.message;
    notificationStore.add(`Falha ao atualizar status: ${errorMessage}`, "error");
  }
};

onMounted(() => {
  fetchRequests();
});
</script>

<template>
  <div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Solicitações de Viagem</h1>

      <div class="flex items-center space-x-2">
        <label class="text-gray-600 font-medium">Filtrar:</label>
        <select
          v-model="filterStatus"
          @change="fetchRequests"
          class="form-select block w-full mt-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5"
        >
          <option value="">Todos</option>
          <option value="requested">Solicitado</option>
          <option value="approved">Aprovado</option>
          <option value="cancelled">Cancelado</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-4 text-gray-500">Carregando solicitações...</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="min-w-full leading-normal">
        <thead>
          <tr>
            <th
              class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
            >
              ID
            </th>
            <th
              class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
            >
              Usuário
            </th>
            <th
              class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
            >
              Destino
            </th>
            <th
              class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
            >
              Datas
            </th>
            <th
              class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
            >
              Status
            </th>
            <th
              v-if="authStore.isAdmin"
              class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
            >
              Ações
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="req in requests" :key="req.id">
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">{{ req.id }}</p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">{{ req.user?.name || "N/A" }}</p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">{{ req.destination }}</p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <p class="text-gray-900 whitespace-no-wrap">
                {{ formatDate(req.departure_date) }} to {{ formatDate(req.return_date) }}
              </p>
            </td>
            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
              <span
                class="relative inline-block px-3 py-1 font-semibold leading-tight rounded-full"
                :class="{
                  'bg-yellow-100 text-yellow-900': req.status === 'requested',
                  'bg-green-100 text-green-900': req.status === 'approved',
                  'bg-red-100 text-red-900': req.status === 'cancelled'
                }"
              >
                <span aria-hidden class="absolute inset-0 opacity-50 rounded-full"></span>
                <span class="relative">{{ statusMap[req.status] || req.status }}</span>
              </span>
            </td>
            <td
              v-if="authStore.isAdmin"
              class="px-5 py-5 border-b border-gray-200 bg-white text-sm"
            >
              <div v-if="req.status === 'requested'" class="flex space-x-2">
                <button
                  @click="openConfirmModal(req.id, 'approved')"
                  class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-150 ease-in-out"
                >
                  Aprovar
                </button>
                <button
                  @click="openConfirmModal(req.id, 'cancelled')"
                  class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-150 ease-in-out"
                >
                  Cancelar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <ConfirmModal
      :is-open="isModalOpen"
      :title="modalTitle"
      :message="modalMessage"
      :type="modalType"
      @confirm="confirmAction"
      @cancel="closeConfirmModal"
    />
  </div>
</template>
