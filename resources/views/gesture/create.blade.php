@extends('layouts.app')

@section('title', 'Crear gesto')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/reconocimiento.css') }}">
@endsection

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Crear Nuevo Gesto</h3>
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">Volver a Gestos</a>
    </div>

    <div class="card">
        <div class="container">
            <!-- Mensaje informativo sobre normalización -->
            <div class="info-banner">
                <div class="info-content">
                    <span class="info-icon">🎯</span>
                    <div class="info-text">
                        <strong>Sistema Mejorado:</strong> Ahora usa normalización de landmarks para reconocimiento independiente del tamaño y posición de la mano.
                    </div>
                    <button id="showNormalizationBannerBtn" class="info-btn">Ver Detalles</button>
                </div>
            </div>

            <!-- Selector de Modo -->
            <div class="mode-selector">
                <button id="captureMode" class="mode-btn active">Modo Registro</button>
                <button id="practiceMode" class="mode-btn">Modo Práctica</button>
                <button id="recognizeMode" class="mode-btn">Modo Reconocimiento</button>
            </div>

            <!-- Modo Captura -->
            <div id="captureModeContent" class="mode-content active">
                <div class="controls">
                    <div class="input-group">
                        <label for="gestureName">Nombre del Gesto:</label>
                        <input type="text" id="gestureName" placeholder="Ej: ¿Dónde vives?, Hola, Gracias">
                    </div>
                    <button id="startSequenceBtn" class="btn btn-primary">Iniciar Secuencia</button>
                    <button id="captureBtn" class="btn btn-success" disabled>Capturar Frame <span
                            id="frameNumber">1</span></button>
                    <button id="finishSequenceBtn" class="btn btn-success" disabled>Finalizar Secuencia</button>
                    <button id="clearFramesBtn" class="btn btn-danger">Limpiar Frames</button>
                </div>
                <div class="sequence-status" id="sequenceStatus">
                    <p><strong>Estado:</strong> <span id="sequenceStatusText">Presiona "Iniciar Secuencia" para
                            comenzar</span></p>
                    <div class="progress-bar hidden" id="progressContainer">
                        <div class="progress-fill" id="progressFill"></div>
                        <span class="progress-text" id="progressText">0/0</span>
                    </div>
                </div>
            </div>

            <!-- Modo Práctica -->
            <div id="practiceModeContent" class="mode-content">
                <div class="controls">
                    <div class="input-group">
                        <label for="practiceGesture">Seleccionar Gesto:</label>
                        <select id="practiceGesture">
                            <option value="">-- Selecciona un gesto --</option>
                        </select>
                    </div>
                    <button id="startPracticeBtn" class="btn btn-primary">Iniciar Práctica</button>
                    <button id="stopPracticeBtn" class="btn btn-danger" disabled>Detener Práctica</button>
                    <div class="input-group">
                        <label for="similarityThreshold">Umbral de Similitud:</label>
                        <input type="range" id="similarityThreshold" min="70" max="95" step="5" value="80">
                        <span id="thresholdValue">80%</span>
                    </div>
                </div>
                <div class="practice-status" id="practiceStatus">
                    <div class="current-frame-indicator" id="currentFrameIndicator">
                        <h3>Frame Objetivo: <span id="targetFrameNumber">-</span></h3>
                        <div class="frame-progress" id="frameProgress"></div>
                    </div>
                    <div class="similarity-display" id="similarityDisplay">
                        <h4>Similitud Actual: <span id="currentSimilarity">0%</span></h4>
                        <div class="similarity-bar-container">
                            <div class="similarity-bar" id="similarityBar"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modo Reconocimiento -->
            <div id="recognizeModeContent" class="mode-content">
                <div class="controls">
                    <button id="startRecognitionBtn" class="btn btn-primary">Iniciar Reconocimiento</button>
                    <button id="stopRecognitionBtn" class="btn btn-danger" disabled>Detener</button>
                    <div class="input-group">
                        <label for="toleranceSlider">Tolerancia:</label>
                        <input type="range" id="toleranceSlider" min="0.1" max="0.9" step="0.1" value="0.7">
                        <span id="toleranceValue">70%</span>
                    </div>
                </div>
            </div>
            <div class="video-container">
                <video id="video" autoplay muted></video>
                <canvas id="outputCanvas"></canvas>
            </div>

            <div class="status">
                <p>Estado: <span id="statusText">Listo para capturar</span></p>
                <p class="frame-count">Frames capturados: <span id="frameCount">0</span></p>
            </div>

            <!-- Resultados de Reconocimiento -->
            <div id="recognitionResults" class="recognition-results">
                <h3>Resultado del Reconocimiento</h3>
                <div class="result-display">
                    <div class="recognized-gesture">
                        <span id="recognizedGesture">---</span>
                    </div>
                    <div class="confidence-display">
                        <div class="confidence-bar-container">
                            <div class="confidence-bar" id="confidenceBar"></div>
                        </div>
                        <span id="confidenceText">0%</span>
                    </div>
                </div>
            </div>

            <div class="gesture-info" id="captureInfo">
                <h3>Frames del Gesto Actual</h3>
                <div id="currentFrames">No hay frames capturados</div>
            </div>

            <div class="gesture-list" id="gestureListSection">
                <h3>Gestos Guardados</h3>
                <div id="savedGestures">No hay gestos guardados</div>
            </div>

            <div class="export-section">
                <button id="saveAllToDBBtn" class="btn" style="background: #28a745; color: white;">💾 Guardar Todos en BD</button>
                <button id="exportAllBtn" class="btn btn-primary">Exportar Todos</button>
                <button id="importGestureBtn" class="btn btn-success">Importar Gesto</button>
                <input type="file" id="importGestureFile" accept=".json" title="Seleccionar archivo JSON de gesto individual">
                <button id="clearAllBtn" class="btn btn-danger">Limpiar Todo</button>
                <button id="showNormalizationBtn" class="btn" style="background: #6f42c1; color: white;">Ver Normalización</button>
            </div>

            <!-- Modal de información de normalización -->
            <div id="normalizationModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>🔬 Normalización de Landmarks</h2>
                    <div class="normalization-info">
                        <h3>¿Por qué normalizar?</h3>
                        <p>La normalización hace que el reconocimiento de gestos sea <strong>independiente del tamaño y posición de la mano</strong>.</p>

                        <h3>Proceso de normalización:</h3>
                        <ol>
                            <li><strong>Calcular el centroide:</strong> Promedio de las coordenadas x e y de los 21 puntos de la mano</li>
                            <li><strong>Trasladar al centro:</strong> Restar el centroide a cada punto (elimina dependencia de posición)</li>
                            <li><strong>Escalar:</strong> Dividir por la distancia máxima al centro (elimina dependencia de tamaño)</li>
                        </ol>

                        <h3>Beneficios:</h3>
                        <ul>
                            <li>✅ Funciona con manos de cualquier tamaño</li>
                            <li>✅ Funciona en cualquier posición de la cámara</li>
                            <li>✅ Mayor precisión en el reconocimiento</li>
                            <li>✅ Gestos más consistentes</li>
                        </ul>

                        <div class="normalization-demo">
                            <h3>Ejemplo visual:</h3>
                            <p><strong>Sin normalización:</strong> Una mano pequeña en la esquina vs una mano grande en el centro = diferentes</p>
                            <p><strong>Con normalización:</strong> Ambas manos se ven iguales al sistema ✨</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>

<!-- Scripts del sistema organizados por funcionalidad -->
<script src="{{ asset('js/auth-helper.js') }}"></script>
<script src="{{ asset('js/reconocimiento/landmark-normalizer.js') }}"></script>
<script src="{{ asset('js/reconocimiento/data-manager.js') }}"></script>
<script src="{{ asset('js/reconocimiento/capture-manager.js') }}"></script>
<script src="{{ asset('js/reconocimiento/practice-manager.js') }}"></script>
<script src="{{ asset('js/reconocimiento/recognition-manager.js') }}"></script>
<script src="{{ asset('js/reconocimiento/ui-manager.js') }}"></script>
<script src="{{ asset('js/reconocimiento/mediapipe-config.js') }}"></script>
<script src="{{ asset('js/reconocimiento/main.js') }}"></script>

<script type="module">
    const gestureForm = document.getElementById('gestureForm');
    const lessonSelect = document.getElementById('lesson_id');
    const token = localStorage.getItem('auth_token');

    async function loadLessons() {
        try {
            const res = await fetch('/api/lessons', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();
            if (data.success && Array.isArray(data.data)) {
                lessonSelect.innerHTML = '<option value="">Seleccionar lección...</option>';
                data.data.forEach(lesson => {
                    const option = new Option(lesson.name, lesson.id);
                    lessonSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    gestureForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const jsonData = JSON.parse(gestureForm.frames.value);

        const formData = {
            lesson_id: parseInt(gestureForm.lesson_id.value),
            gesture_data: {
                name: gestureForm.gesture_name.value.toUpperCase(),
                frames: jsonData.gesture.frames,
                frameCount: jsonData.gesture.frameCount,
                isSequential: jsonData.gesture.isSequential
            }
        };

        const res = await fetch('/api/gestures', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(formData)
        });

        const result = await res.json();

        if (res.ok && result.success) {
            alert('Gesto creado exitosamente');
            window.location.href = '/dashboard/gestures';
        } else {
            alert('Error: ' + (result.message || 'Error al crear el gesto'));
        }
    });

    document.getElementById('gesture_name').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    loadLessons();
</script>
@endsection