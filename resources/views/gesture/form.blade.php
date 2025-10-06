<form id="gestureForm">
    <div class="mb-3">
        <label for="lesson_id" class="form-label">Lección *</label>
        <select name="lesson_id" id="lesson_id" class="form-select" required>
            <option value="">Seleccionar lección...</option>
        </select>
        <div class="form-text">Selecciona la lección a la que pertenece este gesto.</div>
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
        <textarea name="frames" id="frames" class="form-control" rows="6" required 
                  placeholder='[{"id": 1728154221001, "timestamp": "2025-10-05T21:12:00.000Z", "landmarks": [[{"x": 0.7179, "y": 0.7625, "z": 0.0000}]], "gestureName": "HOLA", "frameIndex": 0}]'></textarea>
        <div class="form-text">Pega aquí el array de frames generado por MediaPipe en formato JSON.</div>
    </div>

    <div class="mb-3">
        <label for="isSequential" class="form-label">Tipo de gesto</label>
        <select name="isSequential" id="isSequential" class="form-select">
            <option value="true" selected>Secuencial (movimiento continuo)</option>
            <option value="false">Estático (posición única)</option>
        </select>
        <div class="form-text">
            <strong>Secuencial:</strong> Para gestos con movimiento. 
            <strong>Estático:</strong> Para posiciones fijas.
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Guardar
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
