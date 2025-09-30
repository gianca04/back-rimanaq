import { ref } from 'vue';

// Estado global para las notificaciones
const toasts = ref([]);
let toastId = 0;

export function useToast() {
  
  const addToast = (message, type = 'info', duration = 5000) => {
    const id = ++toastId;
    const toast = {
      id,
      message,
      type,
      duration,
      show: true
    };

    toasts.value.push(toast);

    // Auto-remove toast after duration
    if (duration > 0) {
      setTimeout(() => {
        removeToast(id);
      }, duration);
    }

    return id;
  };

  const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  const success = (message, duration = 5000) => {
    return addToast(message, 'success', duration);
  };

  const error = (message, duration = 7000) => {
    return addToast(message, 'error', duration);
  };

  const info = (message, duration = 5000) => {
    return addToast(message, 'info', duration);
  };

  const clear = () => {
    toasts.value = [];
  };

  return {
    toasts,
    addToast,
    removeToast,
    success,
    error,
    info,
    clear
  };
}