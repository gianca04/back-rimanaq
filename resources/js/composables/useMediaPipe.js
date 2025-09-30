import { ref, readonly, onMounted, onUnmounted } from 'vue';
import mediaPipeLoader from '../mediapipe/mediapipe-loader.js';
import { AdvancedSequenceProcessor } from '../mediapipe/advanced-math-processor.js';

export function useMediaPipe() {
  // Estado reactivo
  const isLoaded = ref(false);
  const isLoading = ref(false);
  const error = ref(null);
  const hands = ref(null);
  const camera = ref(null);
  const landmarks = ref([]);
  const isDetecting = ref(false);

  // Instancia del procesador
  const sequenceProcessor = new AdvancedSequenceProcessor();

  /**
   * Inicializar MediaPipe
   */
  const initialize = async (options = {}) => {
    if (isLoaded.value) return true;

    isLoading.value = true;
    error.value = null;

    try {
      // Cargar MediaPipe
      const loaded = await mediaPipeLoader.loadMediaPipe();
      if (!loaded) {
        throw new Error('No se pudo cargar MediaPipe');
      }

      // Crear instancia de Hands
      hands.value = mediaPipeLoader.createHandsInstance(options);

      // Configurar callback para resultados
      hands.value.onResults(onResults);

      isLoaded.value = true;
      console.log('MediaPipe inicializado correctamente');

    } catch (err) {
      error.value = err.message;
      console.error('Error inicializando MediaPipe:', err);
    } finally {
      isLoading.value = false;
    }

    return isLoaded.value;
  };

  /**
   * Iniciar cámara
   */
  const startCamera = async (videoElement) => {
    if (!isLoaded.value) {
      throw new Error('MediaPipe no está inicializado');
    }

    try {
      camera.value = mediaPipeLoader.createCamera(videoElement, async () => {
        if (hands.value && isDetecting.value) {
          await hands.value.send({ image: videoElement });
        }
      });

      await camera.value.start();
      isDetecting.value = true;
      console.log('Cámara iniciada');

    } catch (err) {
      error.value = err.message;
      console.error('Error iniciando cámara:', err);
      throw err;
    }
  };

  /**
   * Detener cámara
   */
  const stopCamera = () => {
    if (camera.value) {
      camera.value.stop();
      isDetecting.value = false;
      console.log('Cámara detenida');
    }
  };

  /**
   * Callback para resultados de MediaPipe
   */
  const onResults = (results) => {
    landmarks.value = results.multiHandLandmarks || [];
    
    // Emitir evento personalizado para que los componentes puedan reaccionar
    if (typeof window !== 'undefined') {
      window.dispatchEvent(new CustomEvent('mediapipe-results', {
        detail: results
      }));
    }
  };

  /**
   * Capturar frame actual
   */
  const captureCurrentFrame = () => {
    if (!landmarks.value.length) {
      return null;
    }

    return {
      timestamp: Date.now(),
      landmarks: landmarks.value.map(handLandmarks => 
        handLandmarks.map(landmark => ({
          x: landmark.x,
          y: landmark.y,
          z: landmark.z || 0
        }))
      )
    };
  };

  /**
   * Procesar secuencia de frames
   */
  const processSequence = (frames) => {
    if (!frames || frames.length === 0) return null;
    
    return sequenceProcessor.normalizeSequenceTiming(frames);
  };

  /**
   * Comparar dos secuencias
   */
  const compareSequences = (sequence1, sequence2, useProcrustesDistance = false) => {
    if (!sequence1 || !sequence2) return 0;

    if (useProcrustesDistance) {
      // Usar análisis de Procrustes para mayor precisión
      return sequenceProcessor.procrustesDistance(
        sequence1.flatMap(frame => frame.landmarks.flat()),
        sequence2.flatMap(frame => frame.landmarks.flat())
      );
    } else {
      // Usar DTW para comparación temporal
      return sequenceProcessor.dynamicTimeWarping(sequence1, sequence2);
    }
  };

  /**
   * Limpiar recursos
   */
  const cleanup = () => {
    stopCamera();
    
    if (hands.value) {
      hands.value.close();
      hands.value = null;
    }

    isLoaded.value = false;
    isDetecting.value = false;
    landmarks.value = [];
    error.value = null;
  };

  // Limpiar al desmontar
  onUnmounted(() => {
    cleanup();
  });

  // API pública
  return {
    // Estado
    isLoaded,
    isLoading,
    isDetecting,
    error,
    landmarks,

    // Métodos
    initialize,
    startCamera,
    stopCamera,
    captureCurrentFrame,
    processSequence,
    compareSequences,
    cleanup,

    // Instancias (solo lectura)
    hands: readonly(hands),
    camera: readonly(camera)
  };
}