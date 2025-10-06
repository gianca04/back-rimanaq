@extends('layouts.app')

@section('title', 'Lecciones')

@section('content')

<div class="container">
    <h1>Captura de Gestos con MediaPipe</h1>

    <!-- Mensaje informativo sobre normalización -->
    <div class="info-banner">
        <div class="info-content">
            <span class="info-icon">🎯</span>
            <div class="info-text">
                <strong>Sistema Mejorado:</strong> Ahora usa normalización de landmarks para reconocimiento independiente del tamaño y posición de la mano.
            </div>E
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
        <button id="exportBtn" class="btn btn-primary">Exportar Dataset</button>
        <button id="importBtn" class="btn btn-success">Importar Dataset</button>
        <input type="file" id="importFile" accept=".json" title="Seleccionar archivo JSON para importar">
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

@endsection
<style>
    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }

    /* Banner informativo */
    .info-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .info-content {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .info-icon {
        font-size: 1.5rem;
    }

    .info-text {
        flex: 1;
        min-width: 200px;
    }

    .info-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .info-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    .controls {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .input-group label {
        font-weight: bold;
        color: #555;
    }

    input[type="text"] {
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        min-width: 200px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
        transform: translateY(-2px);
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #1e7e34;
        transform: translateY(-2px);
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .video-container {
        position: relative;
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    video {
        display: none;
        /* Ocultar el video original */
    }

    canvas {
        border: 3px solid #ddd;
        border-radius: 10px;
        max-width: 640px;
        max-height: 480px;
    }

    .status {
        text-align: center;
        margin: 20px 0;
    }

    .status p {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .frame-count {
        color: #007bff;
    }

    .gesture-info {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
    }

    .gesture-list {
        max-height: 300px;
        overflow-y: auto;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
    }

    .gesture-item {
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        margin: 5px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .gesture-name {
        font-weight: bold;
        color: #333;
    }

    .frame-info {
        color: #666;
        font-size: 14px;
    }

    .delete-btn {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 3px;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
    }

    .recording {
        border-color: #dc3545 !important;
        animation: recording-pulse 1s infinite;
    }

    @keyframes recording-pulse {

        0%,
        100% {
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
        }

        50% {
            box-shadow: 0 0 20px rgba(220, 53, 69, 0.8);
        }
    }

    .export-section {
        background: #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        text-align: center;
    }

    /* Estilos para modos */
    .mode-selector {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
    }

    .mode-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 25px;
        background: rgba(102, 126, 234, 0.2);
        color: #667eea;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mode-btn.active {
        background: #667eea;
        color: white;
        transform: scale(1.05);
    }

    .mode-btn:hover {
        background: rgba(102, 126, 234, 0.3);
    }

    .mode-btn.active:hover {
        background: #5a6fd8;
    }

    .mode-content {
        display: none;
    }

    .mode-content.active {
        display: block;
    }

    /* Estilos para reconocimiento */
    .recognition-results {
        background: #f8f9fa;
        border: 2px solid #28a745;
        border-radius: 15px;
        padding: 20px;
        margin: 20px 0;
        text-align: center;
        display: none;
    }

    .recognition-results.active {
        display: block;
    }

    .result-display {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .recognized-gesture {
        font-size: 2.5rem;
        font-weight: bold;
        color: #28a745;
        padding: 20px;
        background: white;
        border-radius: 15px;
        min-width: 200px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .confidence-display {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
        max-width: 400px;
    }

    .confidence-bar-container {
        flex: 1;
        height: 20px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .confidence-bar {
        height: 100%;
        background: linear-gradient(90deg, #dc3545, #ffc107, #28a745);
        width: 0%;
        transition: width 0.3s ease;
        border-radius: 10px;
    }

    #confidenceText {
        font-weight: bold;
        font-size: 1.2rem;
        color: #333;
        min-width: 50px;
    }

    /* Slider de tolerancia */
    input[type="range"] {
        width: 150px;
        margin: 0 10px;
    }

    #toleranceValue {
        font-weight: bold;
        color: #667eea;
        min-width: 40px;
    }

    /* Estados de reconocimiento */
    .recognizing {
        border-color: #ffc107 !important;
        animation: recognizing-pulse 1.5s infinite;
    }

    @keyframes recognizing-pulse {

        0%,
        100% {
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
        }

        50% {
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.8);
        }
    }

    /* Ocultar input file */
    #importFile {
        display: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .mode-selector {
            flex-direction: column;
            align-items: center;
        }

        .confidence-display {
            flex-direction: column;
            gap: 10px;
        }

        .recognized-gesture {
            font-size: 2rem;
            min-width: 150px;
        }
    }

    /* Estilos para secuencias y práctica */
    .sequence-status,
    .practice-status {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
    }

    .progress-bar {
        width: 100%;
        height: 25px;
        background: #e9ecef;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #007bff, #28a745);
        border-radius: 12px;
        transition: width 0.3s ease;
        width: 0%;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-weight: bold;
        color: #333;
        z-index: 1;
    }

    .hidden {
        display: none;
    }

    .current-frame-indicator {
        text-align: center;
        padding: 15px;
        background: white;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .frame-progress {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }

    .frame-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #dee2e6;
        border: 2px solid #adb5bd;
        transition: all 0.3s ease;
    }

    .frame-dot.completed {
        background: #28a745;
        border-color: #1e7e34;
    }

    .frame-dot.current {
        background: #ffc107;
        border-color: #e0a800;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }
    }

    .similarity-display {
        text-align: center;
    }

    .similarity-bar-container {
        width: 100%;
        height: 30px;
        background: #e9ecef;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        margin: 10px 0;
    }

    .similarity-bar {
        height: 100%;
        border-radius: 15px;
        transition: all 0.3s ease;
        width: 0%;
    }

    #practiceGesture {
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        min-width: 200px;
    }

    .success-animation {
        border-color: #28a745 !important;
        animation: success-glow 1s ease-in-out;
    }

    @keyframes success-glow {

        0%,
        100% {
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        50% {
            box-shadow: 0 0 25px rgba(40, 167, 69, 0.8);
        }
    }

    /* Estilos para opciones de completación de práctica */
    .practice-completion-options {
        display: none;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin: 20px 0;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        animation: slideInFromBottom 0.5s ease-out;
    }

    @keyframes slideInFromBottom {
        from {
            transform: translateY(50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .completion-message {
        text-align: center;
        margin-bottom: 20px;
    }

    .celebration-icon {
        font-size: 3rem;
        margin-bottom: 10px;
        animation: bounce 1s infinite;
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    .completion-message h3 {
        margin: 10px 0;
        font-size: 1.5rem;
        color: white;
    }

    .completion-message p {
        margin: 10px 0;
        font-size: 1.1rem;
        opacity: 0.95;
    }

    .completion-stats {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 15px 0;
        flex-wrap: wrap;
    }

    .stat-item {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: bold;
        backdrop-filter: blur(5px);
    }

    .next-gesture-section,
    .session-complete-section {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
        backdrop-filter: blur(5px);
    }

    .next-gesture-section h4,
    .session-complete-section h4 {
        margin: 0 0 15px 0;
        color: white;
        text-align: center;
    }

    .next-gesture-info {
        text-align: center;
        margin: 15px 0;
    }

    .next-gesture-name {
        display: block;
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .next-gesture-frames {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .completion-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .completion-actions .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 25px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 160px;
    }

    .completion-actions .btn-primary {
        background: white;
        color: #28a745;
    }

    .completion-actions .btn-primary:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .completion-actions .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .completion-actions .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    /* Responsive para opciones de completación */
    @media (max-width: 768px) {
        .completion-stats {
            flex-direction: column;
            align-items: center;
        }

        .completion-actions {
            flex-direction: column;
            align-items: center;
        }

        .completion-actions .btn {
            min-width: 200px;
        }
    }

    /* Estilos para modal de normalización */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 30px;
        border: none;
        border-radius: 15px;
        width: 80%;
        max-width: 600px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .close:hover,
    .close:focus {
        color: #667eea;
        text-decoration: none;
    }

    .normalization-info h2 {
        color: #667eea;
        text-align: center;
        margin-bottom: 25px;
        font-size: 1.8rem;
    }

    .normalization-info h3 {
        color: #333;
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 1.2rem;
    }

    .normalization-info ol,
    .normalization-info ul {
        padding-left: 20px;
        line-height: 1.6;
    }

    .normalization-info li {
        margin-bottom: 8px;
    }

    .normalization-demo {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }

    .normalization-demo h3 {
        color: white;
        margin-top: 0;
    }

    .normalization-demo p {
        margin: 10px 0;
    }
</style>
<script>
    const apiBase = '/api/gestures';
    const gesturesApiBase = '/api/gestures';
    const token = localStorage.getItem('auth_token');
</script>
<script src="{{ asset('reconocimiento/landmark-normalizer.js') }}"></script>
<script src="{{ asset('reconocimiento/data-manager.js') }}"></script>
<script src="{{ asset('reconocimiento/capture-manager.js') }}"></script>
<script src="{{ asset('reconocimiento/practice-manager.js') }}"></script>
<script src="{{ asset('reconocimiento/recognition-manager.js') }}"></script>
<script src="{{ asset('reconocimiento/ui-manager.js') }}"></script>
<script src="{{ asset('reconocimiento/mediapipe-config.js') }}"></script>
<script src="{{ asset('reconocimiento/main.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>