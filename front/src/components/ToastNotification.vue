<script setup lang="ts">
import { useNotificationStore } from "../stores/notification";

const notificationStore = useNotificationStore();

const getTypeClass = (type: string) => {
  switch (type) {
    case "success":
      return "bg-green-500";
    case "error":
      return "bg-red-500";
    case "info":
    default:
      return "bg-blue-500";
  }
};
</script>

<template>
  <div class="fixed top-4 right-4 z-50 flex flex-col gap-2">
    <transition-group name="toast">
      <div
        v-for="notification in notificationStore.notifications"
        :key="notification.id"
        class="text-white px-6 py-3 rounded shadow-lg flex items-center justify-between min-w-[300px]"
        :class="getTypeClass(notification.type)"
      >
        <span>{{ notification.message }}</span>
        <button
          @click="notificationStore.remove(notification.id)"
          class="ml-4 text-white hover:text-gray-200 focus:outline-none font-bold"
        >
          &times;
        </button>
      </div>
    </transition-group>
  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
