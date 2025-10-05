<form id="gestureForm">
    <div class="mb-3">
        <label for="course_id" class="form-label">Curso *</label>
        <select name="course_id" id="course_id" class="form-select" required disabled>
            <option value="">⏳ Cargando cursos...</option>
            <!-- Los cursos se cargan dinámicamente -->
        </select>
        <div class="invalid-feedback"></div>
        <div class="form-text">Cargando cursos disponibles...</div>
    </div>

    <div class="mb-3">
        <label for="lesson_id" class="form-label">Lección *</label>
        <select name="lesson_id" id="lesson_id" class="form-select" required disabled>
            <option value="">Seleccionar lección...</option>
            <!-- Las lecciones se cargan dinámicamente -->
        </select>
        <div class="invalid-feedback"></div>
        <div class="form-text">Primero selecciona un curso para ver las lecciones disponibles.</div>
    </div>

    <div class="mb-3">
        <label for="gesture_name" class="form-label">Nombre del gesto *</label>
        <input type="text" name="gesture_name" id="gesture_name" class="form-control" required 
               placeholder="Ej: HOLA, GRACIAS, BUENOS_DIAS" maxlength="50">
        <div class="invalid-feedback"></div>
        <div class="form-text">El nombre se convertirá automáticamente a mayúsculas.</div>
    </div>

    <div class="mb-3">
        <label for="frames" class="form-label">Frames (JSON) *</label>
        <textarea name="frames" id="frames" class="form-control" rows="8" required 
                  placeholder='[
  {
    "id": 1728154221001,
    "timestamp": "2025-10-05T21:12:00.000Z",
    "landmarks": [
      [
        {"x": 0.7179, "y": 0.7625, "z": 0.0000},
        {"x": 0.6374, "y": 0.7263, "z": -0.0311}
      ]
    ],
    "gestureName": "HOLA",
    "frameIndex": 0
  }
]'></textarea>
        <div class="form-text">
            Pega aquí el array de frames generado por MediaPipe. Debe ser un JSON válido con la estructura de landmarks.
            <br><small class="text-muted">
                <strong>Estructura requerida:</strong> Cada frame debe contener: id, timestamp, landmarks (array de puntos con x, y, z), gestureName y frameIndex.
            </small>
            <br><small class="text-muted">
                <strong>Tip:</strong> Puedes copiar el JSON directamente desde la consola de MediaPipe o desde un archivo de captura.
            </small>
        </div>
        <div class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="isSequential" class="form-label">Tipo de gesto *</label>
        <select name="isSequential" id="isSequential" class="form-select" required>
            <option value="true" selected>Secuencial (movimiento continuo)</option>
            <option value="false">Estático (posición única)</option>
        </select>
        <div class="form-text">
            <strong>Secuencial:</strong> Para gestos que requieren movimiento (ej: "Hola" con movimiento de mano).
            <br><strong>Estático:</strong> Para gestos de posición fija (ej: letras del alfabeto).
        </div>
    </div>

    <!-- Información del JSON -->
    <div class="mb-3">
        <div class="card border-info">
            <div class="card-body py-2">
                <small class="text-info">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Vista previa del gesto:</strong>
                    <span id="jsonInfo" class="text-muted">Pega el JSON para ver información</span>
                </small>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Guardar gesto
        </button>
    </div>
</form>

<style>
    #lesson_id:disabled {
        background-color: #f8f9fa;
        opacity: 0.65;
    }
    
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .form-text .text-muted {
        font-size: 0.8rem;
    }
</style>
