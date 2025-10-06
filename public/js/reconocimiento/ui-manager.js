// === FUNCIONES DE UI Y UTILIDADES ===

class UIManager {
  constructor(gestureSystem) {
    this.gestureSystem = gestureSystem;
    
    // === CRONÓMETRO ===
    this.timerInterval = null;
    this.timerStartTime = null;
    this.timerElement = null;
    this.timerEnabled = true;
    this.timerDuration = 3000; // 3 segundos por defecto
    this.initializeTimer();
  }

  deleteGesture(gestureId) {
    this.gestureSystem.savedGestures = this.gestureSystem.savedGestures.filter((g) => g.id !== gestureId);
    this.gestureSystem.saveSavedGestures();
    this.updateDisplay();
    this.gestureSystem.practiceManager.updatePracticeGestureList();
    this.gestureSystem.statusText.textContent = "Gesto eliminado";
  }

  clearAllGestures() {
    if (confirm("¿Estás seguro de que quieres eliminar todos los gestos?")) {
      this.gestureSystem.savedGestures = [];
      this.gestureSystem.currentFrames = [];
      this.gestureSystem.captureManager.resetSequenceState();
      this.gestureSystem.saveSavedGestures();
      this.updateDisplay();
      this.gestureSystem.practiceManager.updatePracticeGestureList();
      this.gestureSystem.statusText.textContent = "Todos los gestos eliminados";
    }
  }

  // === FUNCIONES DEL CRONÓMETRO ===
  initializeTimer() {
    // Crear el elemento del cronómetro si no existe
    if (!document.getElementById('captureTimer')) {
      const timerContainer = document.createElement('div');
      timerContainer.id = 'timerContainer';
      timerContainer.className = 'timer-container';
      timerContainer.innerHTML = `
        <div class="timer-controls">
          <label>
            <input type="checkbox" id="timerEnabled" ${this.timerEnabled ? 'checked' : ''}>
            Activar cronómetro
          </label>
          <div class="timer-settings">
            <label for="timerDuration">Duración (ms):</label>
            <input type="number" id="timerDuration" value="${this.timerDuration}" min="1000" max="10000" step="500">
          </div>
        </div>
        <div id="captureTimer" class="capture-timer">
          <div class="timer-display">Listo para capturar</div>
          <div class="timer-progress">
            <div class="timer-bar"></div>
          </div>
          <button id="cancelTimerBtn" class="cancel-timer-btn" style="display: none;">
            Cancelar Cronómetro
          </button>
        </div>
      `;
      
      // Insertar el cronómetro antes del área de captura
      const captureInfo = document.getElementById('captureInfo');
      if (captureInfo && captureInfo.parentNode) {
        captureInfo.parentNode.insertBefore(timerContainer, captureInfo);
      }
      
      // Añadir event listeners
      document.getElementById('timerEnabled').addEventListener('change', (e) => {
        this.timerEnabled = e.target.checked;
        this.updateTimerVisibility();
      });
      
      document.getElementById('timerDuration').addEventListener('change', (e) => {
        this.timerDuration = parseInt(e.target.value);
      });

      document.getElementById('cancelTimerBtn').addEventListener('click', () => {
        this.cancelTimer();
      });
    }
    
    this.timerElement = document.getElementById('captureTimer');
    this.updateTimerVisibility();
  }

  updateTimerVisibility() {
    const timerContainer = document.getElementById('timerContainer');
    if (timerContainer) {
      // Mostrar el contenedor completo solo en modo captura
      if (this.gestureSystem.currentMode === 'capture') {
        timerContainer.style.display = 'block';
        if (this.timerElement) {
          this.timerElement.style.display = this.timerEnabled ? 'block' : 'none';
        }
      } else {
        timerContainer.style.display = 'none';
      }
    }
  }

  startCaptureTimer(callback) {
    if (!this.timerEnabled) {
      // Si el cronómetro está desactivado, ejecutar inmediatamente
      callback();
      return;
    }

    const timerDisplay = this.timerElement.querySelector('.timer-display');
    const timerBar = this.timerElement.querySelector('.timer-bar');
    const cancelBtn = document.getElementById('cancelTimerBtn');
    
    let countdown = Math.ceil(this.timerDuration / 1000);
    timerDisplay.textContent = `Capturando en ${countdown}...`;
    
    // Mostrar botón de cancelación
    if (cancelBtn) {
      cancelBtn.style.display = 'block';
    }
    
    // Reset de la barra de progreso
    timerBar.style.width = '0%';
    timerBar.style.backgroundColor = '#ff6b6b';
    
    this.timerStartTime = Date.now();
    
    this.timerInterval = setInterval(() => {
      const elapsed = Date.now() - this.timerStartTime;
      const remaining = this.timerDuration - elapsed;
      const progress = (elapsed / this.timerDuration) * 100;
      
      // Actualizar barra de progreso
      timerBar.style.width = Math.min(progress, 100) + '%';
      
      // Cambiar color según el progreso
      if (progress > 75) {
        timerBar.style.backgroundColor = '#51cf66';
      } else if (progress > 50) {
        timerBar.style.backgroundColor = '#ffd43b';
      }
      
      if (remaining <= 0) {
        this.stopCaptureTimer();
        timerDisplay.textContent = '¡Capturando!';
        timerBar.style.backgroundColor = '#51cf66';
        timerBar.style.width = '100%';
        
        // Ejecutar callback después de un breve momento
        setTimeout(() => {
          callback();
          this.resetCaptureTimer();
        }, 200);
      } else {
        const secondsLeft = Math.ceil(remaining / 1000);
        timerDisplay.textContent = `Capturando en ${secondsLeft}...`;
      }
    }, 100);
  }

  stopCaptureTimer() {
    if (this.timerInterval) {
      clearInterval(this.timerInterval);
      this.timerInterval = null;
    }
  }

  resetCaptureTimer() {
    this.stopCaptureTimer();
    if (this.timerElement) {
      const timerDisplay = this.timerElement.querySelector('.timer-display');
      const timerBar = this.timerElement.querySelector('.timer-bar');
      const cancelBtn = document.getElementById('cancelTimerBtn');
      
      timerDisplay.textContent = 'Listo para capturar';
      timerBar.style.width = '0%';
      timerBar.style.backgroundColor = '#ff6b6b';
      
      // Ocultar botón de cancelación
      if (cancelBtn) {
        cancelBtn.style.display = 'none';
      }
    }
  }

  // Método público para usar antes de capturar un frame
  captureWithTimer(captureCallback) {
    if (this.timerEnabled) {
      this.gestureSystem.statusText.textContent = 'Preparándose para capturar...';
      this.startCaptureTimer(captureCallback);
    } else {
      captureCallback();
    }
  }

  // Método para cancelar el cronómetro
  cancelTimer() {
    this.stopCaptureTimer();
    this.resetCaptureTimer();
    this.gestureSystem.statusText.textContent = 'Captura cancelada';
  }

  // Método para verificar si el cronómetro está activo
  isTimerRunning() {
    return this.timerInterval !== null;
  }

  // === FUNCIONES DE MODO ===
  switchMode(mode) {
    this.gestureSystem.currentMode = mode;

    // Detener cualquier proceso activo
    this.gestureSystem.recognitionManager.stopRecognition();
    this.gestureSystem.practiceManager.stopPractice();
    this.gestureSystem.captureManager.resetSequenceState();
    
    // Detener cronómetro si está activo
    this.stopCaptureTimer();
    this.resetCaptureTimer();

    // Actualizar botones de modo
    document
      .querySelectorAll(".mode-btn")
      .forEach((btn) => btn.classList.remove("active"));
    document
      .querySelectorAll(".mode-content")
      .forEach((content) => content.classList.remove("active"));

    // Activar modo seleccionado
    document.getElementById(mode + "Mode").classList.add("active");
    document.getElementById(mode + "ModeContent").classList.add("active");

    // Mostrar/ocultar elementos según el modo
    if (mode === "capture") {
      document.getElementById("captureInfo").style.display = "block";
      document.getElementById("gestureListSection").style.display = "block";
      document.getElementById("recognitionResults").classList.remove("active");
      
      // Inicializar cronómetro para el modo de captura
      this.initializeTimer();
      this.updateTimerVisibility();
      
      this.gestureSystem.statusText.textContent =
        "Modo registro activado - Captura gestos secuenciales";
    } else if (mode === "practice") {
      document.getElementById("captureInfo").style.display = "none";
      document.getElementById("gestureListSection").style.display = "none";
      document.getElementById("recognitionResults").classList.remove("active");
      
      // Ocultar cronómetro en modo práctica
      const timerContainer = document.getElementById('timerContainer');
      if (timerContainer) {
        timerContainer.style.display = 'none';
      }
      
      this.gestureSystem.practiceManager.updatePracticeGestureList();

      if (this.gestureSystem.savedGestures.length === 0) {
        this.gestureSystem.statusText.textContent = "No hay gestos guardados para practicar";
      } else {
        this.gestureSystem.statusText.textContent =
          "Modo práctica activado - Selecciona un gesto para practicar";
      }
    } else if (mode === "recognize") {
      document.getElementById("captureInfo").style.display = "none";
      document.getElementById("gestureListSection").style.display = "none";
      document.getElementById("recognitionResults").classList.add("active");
      
      // Ocultar cronómetro en modo reconocimiento
      const timerContainer = document.getElementById('timerContainer');
      if (timerContainer) {
        timerContainer.style.display = 'none';
      }
      
      this.gestureSystem.statusText.textContent = "Modo reconocimiento activado";

      if (this.gestureSystem.savedGestures.length === 0) {
        this.gestureSystem.statusText.textContent = "No hay gestos guardados para reconocer";
      }
    }
  }

  updateDisplay() {
    // Actualizar contador de frames
    this.gestureSystem.frameCountSpan.textContent = this.gestureSystem.currentFrames.length;

    // Habilitar/deshabilitar botón de finalizar secuencia
    if (document.getElementById("finishSequenceBtn")) {
      document.getElementById("finishSequenceBtn").disabled =
        this.gestureSystem.currentFrames.length === 0 || !this.gestureSystem.isRecordingSequence;
    }

    // Actualizar lista de frames actuales
    const currentFramesDiv = document.getElementById("currentFrames");
    if (this.gestureSystem.currentFrames.length === 0) {
      currentFramesDiv.innerHTML = "No hay frames capturados";
    } else {
      currentFramesDiv.innerHTML = this.gestureSystem.currentFrames
        .map(
          (frame, index) =>
            `<div class="gesture-item">
                            <span>Frame ${index + 1}</span>
                            <span class="frame-info">${new Date(
                              frame.timestamp
                            ).toLocaleTimeString()}</span>
                        </div>`
        )
        .join("");
    }

    // Actualizar lista de gestos guardados
    const savedGesturesDiv = document.getElementById("savedGestures");
    if (this.gestureSystem.savedGestures.length === 0) {
      savedGesturesDiv.innerHTML = "No hay gestos guardados";
    } else {
      savedGesturesDiv.innerHTML = this.gestureSystem.savedGestures
        .map(
          (gesture, index) =>
            `<div class="gesture-item">
                            <div>
                                <span class="gesture-name">${
                                  gesture.name
                                }</span>
                                <div class="frame-info">
                                    ${gesture.frameCount} frames${
              gesture.isSequential ? " (secuencial)" : ""
            } - 
                                    ${new Date(
                                      gesture.createdAt
                                    ).toLocaleDateString()}
                                </div>
                            </div>
                            <div class="gesture-actions">
                                <button class="save-btn" onclick="gestureSystem.dataManager.showSaveGestureModal(${index})" title="Guardar en base de datos">💾</button>
                                <button class="export-btn" onclick="gestureSystem.dataManager.exportGesture(${index})" title="Exportar gesto">📤</button>
                                <button class="delete-btn" onclick="gestureSystem.uiManager.deleteGesture(${
                                  gesture.id
                                })" title="Eliminar gesto">🗑️</button>
                            </div>
                        </div>`
        )
        .join("");
    }
  }

  // === FUNCIONES DEL MODAL DE NORMALIZACIÓN ===
  showNormalizationModal() {
    const modal = document.getElementById("normalizationModal");
    modal.style.display = "block";
    
    // Añadir información en tiempo real si hay manos detectadas
    if (this.gestureSystem.lastResults && this.gestureSystem.lastResults.multiHandLandmarks) {
      this.showLiveNormalizationDemo();
    }
  }

  hideNormalizationModal() {
    const modal = document.getElementById("normalizationModal");
    modal.style.display = "none";
  }

  showLiveNormalizationDemo() {
    // Esta función podría mostrar en tiempo real cómo se ven los landmarks 
    // antes y después de la normalización
    console.log("🔬 Demostración de normalización en tiempo real");
    
    if (this.gestureSystem.lastResults && this.gestureSystem.lastResults.multiHandLandmarks) {
      const landmarks = this.gestureSystem.lastResults.multiHandLandmarks[0];
      const centroide = this.gestureSystem.landmarkNormalizer.calcularCentroide(landmarks);
      const landmarksNormalizados = this.gestureSystem.landmarkNormalizer.normalizarLandmarks(landmarks);
      
      console.log("📍 Centroide calculado:", centroide);
      console.log("📊 Landmarks originales (primeros 3):", landmarks.slice(0, 3));
      console.log("🎯 Landmarks normalizados (primeros 3):", landmarksNormalizados.slice(0, 3));
      
      // Mostrar estadísticas en la consola
      const rangoOriginalX = {
        min: Math.min(...landmarks.map(lm => lm.x)),
        max: Math.max(...landmarks.map(lm => lm.x))
      };
      const rangoNormalizadoX = {
        min: Math.min(...landmarksNormalizados.map(lm => lm.x)),
        max: Math.max(...landmarksNormalizados.map(lm => lm.x))
      };
      
      console.log("📏 Rango X original:", rangoOriginalX);
      console.log("🎯 Rango X normalizado:", rangoNormalizadoX);
    }
  }
}
