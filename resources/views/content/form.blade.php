@php
// Recibir el JSON desde el include y convertirlo a array
$contentJson = $contentJson ?? '[]';
$content = json_decode($contentJson, true) ?? [];
@endphp

@include('components.toastify')

<div id="content-form" class="container">
    <div class="accordion" id="contentAccordion">
        @foreach ($content as $index => $item)
        <div class="accordion-item content-item" data-index="{{ $index }}">
            <h2 class="accordion-header" id="heading{{ $index }}">
                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" 
                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" 
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                    {{ $item['titulo'] ?? 'Contenido ' . ($index + 1) }}
                </button>
            </h2>
            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                 aria-labelledby="heading{{ $index }}" data-bs-parent="#contentAccordion">
                <div class="accordion-body">
                    <div class="mb-2">
                        <label for="content[{{ $index }}][titulo]" class="form-label">Título:</label>
                        <input type="text" class="form-control titulo-input" name="content[{{ $index }}][titulo]" 
                               value="{{ $item['titulo'] ?? '' }}">
                    </div>
                    <div class="mb-2">
                        <label for="content[{{ $index }}][descripcion]" class="form-label">Descripción:</label>
                        <textarea class="form-control" name="content[{{ $index }}][descripcion]">{{ $item['descripcion'] ?? '' }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label for="content[{{ $index }}][contenido]" class="form-label">Contenido:</label>
                        <textarea class="form-control" name="content[{{ $index }}][contenido]">{{ $item['contenido'] ?? '' }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label for="content[{{ $index }}][media][tipo]" class="form-label">Media Tipo:</label>
                        <select class="form-select" name="content[{{ $index }}][media][tipo]">
                            <option value="image" {{ ($item['media']['tipo'] ?? '') == 'image' ? 'selected' : '' }}>Imagen</option>
                            <option value="video" {{ ($item['media']['tipo'] ?? '') == 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="content[{{ $index }}][media][url]" class="form-label">Media URL:</label>
                        <input type="text" class="form-control" name="content[{{ $index }}][media][url]" 
                               value="{{ $item['media']['url'] ?? '' }}">
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <button type="button" class="btn btn-success btn-sm save-content">Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm remove-content">Eliminar</button>
                        <div>
                            <button type="button" class="btn btn-secondary btn-sm move-up">Subir</button>
                            <button type="button" class="btn btn-secondary btn-sm move-down">Bajar</button>
                        </div>
                    </div>
                    <div class="preview" style="display: none;">
                        <h5>Previsualización:</h5>
                        <div class="preview-content"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <button type="button" id="add-content" class="btn btn-primary mt-3">Añadir Registro</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contentAccordion = document.getElementById('contentAccordion');
        const addContentButton = document.getElementById('add-content');

        // Definir funciones primero antes de usarlas
        
        // Función para convertir el array del formulario a JSON
        function contentArrayToJson() {
            const contentArray = [];
            const contentItems = contentAccordion.querySelectorAll('.content-item');
            
            contentItems.forEach((item, index) => {
                const titulo = item.querySelector(`[name="content[${index}][titulo]"]`)?.value || '';
                const descripcion = item.querySelector(`[name="content[${index}][descripcion]"]`)?.value || '';
                const contenido = item.querySelector(`[name="content[${index}][contenido]"]`)?.value || '';
                const mediaTipo = item.querySelector(`[name="content[${index}][media][tipo]"]`)?.value || 'image';
                const mediaUrl = item.querySelector(`[name="content[${index}][media][url]"]`)?.value || '';
                
                contentArray.push({
                    index: index,
                    titulo: titulo.trim(),
                    descripcion: descripcion.trim(),
                    contenido: contenido.trim(),
                    media: {
                        tipo: mediaTipo,
                        url: mediaUrl.trim()
                    }
                });
            });
            
            return JSON.stringify(contentArray);
        }

        // Función para actualizar el campo hidden con el JSON actual
        function updateHiddenJson() {
            const hiddenField = document.getElementById('content');
            if (hiddenField) {
                const jsonData = contentArrayToJson();
                hiddenField.value = jsonData || '[]';
                console.log('JSON actualizado:', hiddenField.value);
            }
        }

        // Función para convertir JSON a array y poblar el formulario
        function jsonToContentArray(jsonString) {
            try {
                const contentData = JSON.parse(jsonString || '[]');
                
                // Limpiar el contenido existente
                contentAccordion.innerHTML = '';
                
                // Crear elementos para cada item en el JSON
                contentData.forEach((item, index) => {
                    createContentItem(index, item);
                });
                
                // Si no hay contenido, crear uno vacío
                if (contentData.length === 0) {
                    createContentItem(0, {});
                }
                
            } catch (error) {
                console.error('Error al parsear JSON:', error);
                // En caso de error, crear un item vacío
                contentAccordion.innerHTML = '';
                createContentItem(0, {});
            }
        }

        // Función para crear un item de contenido
        function createContentItem(index, item = {}) {
            const newItem = document.createElement('div');
            newItem.classList.add('accordion-item', 'content-item');
            newItem.setAttribute('data-index', index);
            newItem.innerHTML = `
                <h2 class="accordion-header" id="heading${index}">
                    <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#collapse${index}" 
                            aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapse${index}">
                        ${item.titulo || 'Contenido ' + (index + 1)}
                    </button>
                </h2>
                <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                     aria-labelledby="heading${index}" data-bs-parent="#contentAccordion">
                    <div class="accordion-body">
                        <div class="mb-2">
                            <label for="content[${index}][titulo]" class="form-label">Título:</label>
                            <input type="text" class="form-control titulo-input" name="content[${index}][titulo]" 
                                   value="${item.titulo || ''}">
                        </div>
                        <div class="mb-2">
                            <label for="content[${index}][descripcion]" class="form-label">Descripción:</label>
                            <textarea class="form-control" name="content[${index}][descripcion]">${item.descripcion || ''}</textarea>
                        </div>
                        <div class="mb-2">
                            <label for="content[${index}][contenido]" class="form-label">Contenido:</label>
                            <textarea class="form-control" name="content[${index}][contenido]">${item.contenido || ''}</textarea>
                        </div>
                        <div class="mb-2">
                            <label for="content[${index}][media][tipo]" class="form-label">Media Tipo:</label>
                            <select class="form-select" name="content[${index}][media][tipo]">
                                <option value="image" ${(item.media?.tipo === 'image') ? 'selected' : ''}>Imagen</option>
                                <option value="video" ${(item.media?.tipo === 'video') ? 'selected' : ''}>Video</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="content[${index}][media][url]" class="form-label">Media URL:</label>
                            <input type="text" class="form-control" name="content[${index}][media][url]" 
                                   value="${item.media?.url || ''}">
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <button type="button" class="btn btn-success btn-sm save-content">Guardar</button>
                            <button type="button" class="btn btn-danger btn-sm remove-content">Eliminar</button>
                            <div>
                                <button type="button" class="btn btn-secondary btn-sm move-up">Subir</button>
                                <button type="button" class="btn btn-secondary btn-sm move-down">Bajar</button>
                            </div>
                        </div>
                        <div class="preview" style="display: none;">
                            <h5>Previsualización:</h5>
                            <div class="preview-content"></div>
                        </div>
                    </div>
                </div>
            `;
            contentAccordion.appendChild(newItem);
            attachEventListeners(newItem);
            return newItem;
        }

        // Hacer funciones globales para uso externo
        window.contentArrayToJson = contentArrayToJson;
        window.updateHiddenJson = updateHiddenJson;
        window.jsonToContentArray = jsonToContentArray;



        // Inicializar con datos existentes o crear formulario vacío
        const initialContent = document.getElementById('content');
        if (initialContent && initialContent.value && initialContent.value !== '[]') {
            jsonToContentArray(initialContent.value);
        } else {
            // Si no hay contenido inicial y no hay elementos existentes, crear uno vacío
            if (contentAccordion.children.length === 0) {
                createContentItem(0, {});
            }
        }
        
        // Actualizar JSON inicial al cargar la página
        updateHiddenJson();

        addContentButton.addEventListener('click', function() {
            const index = contentAccordion.children.length;
            const newItem = createContentItem(index, {});
            
            // Abrir automáticamente el nuevo item
            const newCollapse = new bootstrap.Collapse(newItem.querySelector('.accordion-collapse'));
            
            // Actualizar JSON hidden
            updateHiddenJson();
            
            // Mostrar notificación de éxito
            window.showToast('Nuevo contenido añadido correctamente', 'success');
        });

        function attachEventListeners(item) {
            // Actualizar título del acordeón cuando se cambia el input
            const titleInput = item.querySelector('.titulo-input');
            const accordionButton = item.querySelector('.accordion-button');
            
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    const newTitle = this.value || `Contenido ${Array.from(contentAccordion.children).indexOf(item) + 1}`;
                    accordionButton.textContent = newTitle;
                    updateHiddenJson();
                });
            }

            // Agregar listeners para todos los campos de entrada
            const allInputs = item.querySelectorAll('input, textarea, select');
            allInputs.forEach(input => {
                input.addEventListener('input', function() {
                    updateHiddenJson();
                });
                input.addEventListener('change', function() {
                    updateHiddenJson();
                });
            });

            item.querySelector('.save-content').addEventListener('click', function() {
                const mediaType = item.querySelector('[name$="[media][tipo]"]').value;
                const mediaUrl = item.querySelector('[name$="[media][url]"]').value;
                const previewContainer = item.querySelector('.preview');
                const previewContent = item.querySelector('.preview-content');

                if (mediaType && mediaUrl) {
                    previewContent.innerHTML = '';
                    if (mediaType === 'image') {
                        const img = document.createElement('img');
                        img.src = mediaUrl;
                        img.alt = 'Previsualización de imagen';
                        img.style.maxWidth = '100%';
                        img.classList.add('img-fluid');
                        previewContent.appendChild(img);
                    } else if (mediaType === 'video') {
                        if (mediaUrl.includes('youtube.com') || mediaUrl.includes('youtu.be')) {
                            const videoId = extractYouTubeId(mediaUrl);
                            if (videoId) {
                                const iframe = document.createElement('iframe');
                                iframe.src = `https://www.youtube.com/embed/${videoId}`;
                                iframe.width = '100%';
                                iframe.height = '315';
                                iframe.frameBorder = '0';
                                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                                iframe.allowFullscreen = true;
                                previewContent.appendChild(iframe);
                            } else {
                                window.showToast('URL de YouTube no válida.', 'error');
                            }
                        } else {
                            const video = document.createElement('video');
                            video.src = mediaUrl;
                            video.controls = true;
                            video.style.maxWidth = '100%';
                            video.classList.add('img-fluid');
                            previewContent.appendChild(video);
                        }
                    }
                    previewContainer.style.display = 'block';
                    updateHiddenJson();
                    window.showToast('Contenido guardado y previsualizado correctamente', 'success');
                } else {
                    window.showToast('Por favor, complete el tipo y la URL de media para previsualizar.', 'warning');
                }
            });

            item.querySelector('.remove-content').addEventListener('click', function() {
                if (confirm('¿Estás seguro de que quieres eliminar este contenido?')) {
                    item.remove();
                    updateIndexes();
                    updateHiddenJson();
                    window.showToast('Contenido eliminado correctamente', 'info');
                }
            });

            item.querySelector('.move-up').addEventListener('click', function() {
                const prev = item.previousElementSibling;
                if (prev) {
                    contentAccordion.insertBefore(item, prev);
                    updateIndexes();
                    updateHiddenJson();
                    window.showToast('Elemento movido hacia arriba', 'info');
                } else {
                    window.showToast('El elemento ya está en la primera posición', 'warning');
                }
            });

            item.querySelector('.move-down').addEventListener('click', function() {
                const next = item.nextElementSibling;
                if (next) {
                    contentAccordion.insertBefore(next, item);
                    updateIndexes();
                    updateHiddenJson();
                    window.showToast('Elemento movido hacia abajo', 'info');
                } else {
                    window.showToast('El elemento ya está en la última posición', 'warning');
                }
            });
        }

        function updateIndexes() {
            Array.from(contentAccordion.children).forEach((item, index) => {
                item.setAttribute('data-index', index);
                const inputs = item.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
                
                // Actualizar IDs del acordeón
                const header = item.querySelector('.accordion-header');
                const button = item.querySelector('.accordion-button');
                const collapse = item.querySelector('.accordion-collapse');
                
                if (header) header.id = `heading${index}`;
                if (collapse) {
                    collapse.id = `collapse${index}`;
                    button.setAttribute('data-bs-target', `#collapse${index}`);
                    button.setAttribute('aria-controls', `collapse${index}`);
                }
            });
        }

        function extractYouTubeId(url) {
            const regex = /(?:https?:\/\/)?(?:www\.|m\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/live\/)([a-zA-Z0-9_-]{11})/;
            const match = url.match(regex);
            return match ? match[1] : null;
        }

        // Adjuntar listeners a elementos existentes
        Array.from(contentAccordion.children).forEach(attachEventListeners);
    });
</script>