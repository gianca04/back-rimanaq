/**
 * Content CRUD Manager - Gestor completo para el CRUD del contenido de lecciones
 * Maneja la creación, edición, eliminación y reordenamiento de elementos de contenido
 */

class ContentCRUDManager {
    constructor() {
        this.contentArray = [];
        this.currentIndex = 0;
        this.container = null;
        this.template = null;
        this.hiddenField = null;
        this.initialized = false;

        console.log('🎯 ContentCRUDManager inicializado');
    }

    /**
     * Inicializar el manager
     */
    init() {
        if (this.initialized) {
            console.warn('⚠️ ContentCRUDManager ya está inicializado');
            return;
        }

        this.container = document.getElementById('content-items-container');
        this.template = document.getElementById('content-item-template');
        this.hiddenField = document.getElementById('content');
        this.addButton = document.getElementById('add-content-item');

        if (!this.container || !this.template || !this.hiddenField) {
            console.error('❌ No se encontraron elementos requeridos para ContentCRUDManager');
            return false;
        }

        this.setupEvents();
        this.initialized = true;
        console.log('✅ ContentCRUDManager inicializado correctamente');
        return true;
    }

    /**
     * Configurar eventos globales
     */
    setupEvents() {
        // Botón agregar contenido
        if (this.addButton) {
            this.addButton.addEventListener('click', () => {
                this.addItem();
            });
        }

        // Observar cambios en el contenedor para auto-actualizar
        if (window.MutationObserver) {
            const observer = new MutationObserver(() => {
                this.updateHiddenField();
            });
            observer.observe(this.container, { 
                childList: true, 
                subtree: true, 
                attributes: true,
                characterData: true 
            });
        }
    }

    /**
     * Agregar nuevo item de contenido
     */
    addItem(data = null) {
        console.log('➕ Agregando nuevo item de contenido...');
        
        const newItem = this.template.content.cloneNode(true);
        const itemDiv = newItem.querySelector('.content-item');
        
        // Generar índice único
        const index = data ? data.index : this.getNextIndex();
        itemDiv.setAttribute('data-index', index);
        
        // Configurar item
        this.setupItemContent(itemDiv, index, data);
        
        // Agregar al contenedor
        this.container.appendChild(newItem);
        
        // Configurar eventos del nuevo item
        this.setupItemEvents(itemDiv);
        
        // Actualizar datos
        this.updateContentArray();
        
        console.log('✅ Item agregado con índice:', index);
        
        // Scroll hacia el nuevo item
        setTimeout(() => {
            itemDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);

        return itemDiv;
    }

    /**
     * Obtener el siguiente índice disponible
     */
    getNextIndex() {
        const existingIndices = Array.from(this.container.querySelectorAll('.content-item'))
            .map(item => parseInt(item.getAttribute('data-index')))
            .filter(index => !isNaN(index));
        
        return existingIndices.length > 0 ? Math.max(...existingIndices) + 1 : 0;
    }

    /**
     * Configurar contenido de un item
     */
    setupItemContent(itemDiv, index, data = null) {
        // Actualizar badge del índice
        const indexBadge = itemDiv.querySelector('.content-index-badge');
        indexBadge.textContent = `Item #${index}`;
        
        // Actualizar campo índice
        const indexInput = itemDiv.querySelector('.content-index');
        indexInput.value = index;
        
        // Si hay data, llenar los campos
        if (data) {
            this.fillItemWithData(itemDiv, data);
        } else {
            // Valores por defecto para nuevo item
            itemDiv.querySelector('.content-titulo').value = '';
            itemDiv.querySelector('.content-descripcion').value = '';
            itemDiv.querySelector('.content-contenido').value = '';
            itemDiv.querySelector('.content-media-tipo').value = '';
            itemDiv.querySelector('.content-media-url').value = '';
        }
        
        this.updateItemTitle(itemDiv);
    }

    /**
     * Llenar item con datos existentes
     */
    fillItemWithData(itemDiv, data) {
        console.log('📝 Llenando item con datos:', data);
        
        itemDiv.querySelector('.content-titulo').value = data.titulo || '';
        itemDiv.querySelector('.content-descripcion').value = data.descripcion || '';
        itemDiv.querySelector('.content-contenido').value = data.contenido || '';
        
        // Media data
        if (data.media) {
            itemDiv.querySelector('.content-media-tipo').value = data.media.tipo || '';
            itemDiv.querySelector('.content-media-url').value = data.media.url || '';
        }
        
        // Actualizar título en el header
        this.updateItemTitle(itemDiv);
    }

    /**
     * Configurar eventos de un item específico
     */
    setupItemEvents(itemDiv) {
        const index = itemDiv.getAttribute('data-index');
        console.log('🔧 Configurando eventos para item:', index);
        
        // Toggle collapse
        const toggleBtn = itemDiv.querySelector('.toggle-content-item');
        toggleBtn?.addEventListener('click', () => this.toggleItem(itemDiv));
        
        // Delete
        const deleteBtn = itemDiv.querySelector('.delete-content-item');
        deleteBtn?.addEventListener('click', () => this.deleteItem(itemDiv));
        
        // Move up
        const moveUpBtn = itemDiv.querySelector('.move-up-content');
        moveUpBtn?.addEventListener('click', () => this.moveItemUp(itemDiv));
        
        // Move down
        const moveDownBtn = itemDiv.querySelector('.move-down-content');
        moveDownBtn?.addEventListener('click', () => this.moveItemDown(itemDiv));
        
        // Preview media
        const previewBtn = itemDiv.querySelector('.preview-media');
        previewBtn?.addEventListener('click', () => this.previewMedia(itemDiv));
        
        // Auto-update en cambios de campo
        const inputs = itemDiv.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                this.updateItemTitle(itemDiv);
                this.updateContentArray();
            });
            
            input.addEventListener('change', () => {
                this.updateItemTitle(itemDiv);
                this.updateContentArray();
            });
        });
    }

    /**
     * Toggle item collapse
     */
    toggleItem(itemDiv) {
        itemDiv.classList.toggle('collapsed');
        const icon = itemDiv.querySelector('.toggle-content-item i');
        
        if (itemDiv.classList.contains('collapsed')) {
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-right');
        } else {
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        }
    }

    /**
     * Eliminar item
     */
    deleteItem(itemDiv) {
        const index = itemDiv.getAttribute('data-index');
        const titulo = itemDiv.querySelector('.content-titulo').value || 'Sin título';
        
        if (confirm(`¿Estás seguro de eliminar "${titulo}"?`)) {
            console.log('🗑️ Eliminando item:', index);
            
            // Animación de salida
            itemDiv.style.transition = 'all 0.3s ease';
            itemDiv.style.opacity = '0';
            itemDiv.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                itemDiv.remove();
                this.updateContentArray();
                this.reindexItems();
                
                if (window.showToast) {
                    window.showToast(`"${titulo}" eliminado correctamente`, 'info');
                }
            }, 300);
        }
    }

    /**
     * Mover item hacia arriba
     */
    moveItemUp(itemDiv) {
        const previous = itemDiv.previousElementSibling;
        if (previous && previous.classList.contains('content-item')) {
            console.log('⬆️ Moviendo item hacia arriba');
            this.container.insertBefore(itemDiv, previous);
            this.updateContentArray();
            this.reindexItems();
            
            // Highlight temporal
            this.highlightItem(itemDiv);
        }
    }

    /**
     * Mover item hacia abajo  
     */
    moveItemDown(itemDiv) {
        const next = itemDiv.nextElementSibling;
        if (next && next.classList.contains('content-item')) {
            console.log('⬇️ Moviendo item hacia abajo');
            this.container.insertBefore(next, itemDiv);
            this.updateContentArray();
            this.reindexItems();
            
            // Highlight temporal
            this.highlightItem(itemDiv);
        }
    }

    /**
     * Highlight temporal de un item
     */
    highlightItem(itemDiv) {
        itemDiv.style.backgroundColor = '#e3f2fd';
        setTimeout(() => {
            itemDiv.style.backgroundColor = '';
        }, 1000);
    }

    /**
     * Preview del media
     */
    previewMedia(itemDiv) {
        const mediaUrl = itemDiv.querySelector('.content-media-url').value;
        const mediaType = itemDiv.querySelector('.content-media-tipo').value;
        const previewDiv = itemDiv.querySelector('.media-preview');
        const previewContent = itemDiv.querySelector('.media-preview-content');
        
        if (!mediaUrl || !mediaType) {
            if (window.showToast) {
                window.showToast('Por favor, completa el tipo y URL del media', 'warning');
            } else {
                alert('Por favor, completa el tipo y URL del media');
            }
            return;
        }
        
        console.log('👁️ Generando preview para:', mediaType, mediaUrl);
        
        previewContent.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Cargando...</div>';
        previewDiv.style.display = 'block';
        
        try {
            if (mediaType === 'image') {
                const img = new Image();
                img.onload = () => {
                    previewContent.innerHTML = `<img src="${mediaUrl}" class="img-fluid" alt="Preview" style="max-height: 200px;">`;
                };
                img.onerror = () => {
                    previewContent.innerHTML = '<p class="text-danger">Error al cargar la imagen</p>';
                };
                img.src = mediaUrl;
                
            } else if (mediaType === 'video') {
                if (mediaUrl.includes('youtube.com') || mediaUrl.includes('youtu.be')) {
                    const videoId = this.extractYouTubeID(mediaUrl);
                    if (videoId) {
                        previewContent.innerHTML = `
                            <iframe width="100%" height="200" 
                                    src="https://www.youtube.com/embed/${videoId}" 
                                    frameborder="0" allowfullscreen></iframe>
                        `;
                    } else {
                        previewContent.innerHTML = '<p class="text-danger">URL de YouTube no válida</p>';
                    }
                } else {
                    previewContent.innerHTML = `<video controls style="width: 100%; max-height: 200px;"><source src="${mediaUrl}"></video>`;
                }
                
            } else if (mediaType === 'audio') {
                previewContent.innerHTML = `<audio controls style="width: 100%;"><source src="${mediaUrl}"></audio>`;
                
            } else if (mediaType === 'document') {
                previewContent.innerHTML = `
                    <div class="text-center">
                        <a href="${mediaUrl}" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark"></i> Ver documento
                        </a>
                    </div>
                `;
            }
            
        } catch (error) {
            console.error('Error en preview:', error);
            previewContent.innerHTML = '<p class="text-danger">Error al cargar preview</p>';
        }
    }

    /**
     * Extraer ID de YouTube
     */
    extractYouTubeID(url) {
        const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
        const match = url.match(regex);
        return match ? match[1] : null;
    }

    /**
     * Actualizar título del item en el header
     */
    updateItemTitle(itemDiv) {
        const titulo = itemDiv.querySelector('.content-titulo').value;
        const titleDisplay = itemDiv.querySelector('.content-title-display');
        titleDisplay.textContent = titulo || 'Sin título';
        
        // Actualizar también el tooltip
        titleDisplay.title = titulo || 'Sin título';
    }

    /**
     * Reindexar items después de cambios de orden
     */
    reindexItems() {
        console.log('🔢 Reindexando items...');
        const items = this.container.querySelectorAll('.content-item');
        
        items.forEach((item, index) => {
            item.setAttribute('data-index', index);
            item.querySelector('.content-index-badge').textContent = `Item #${index}`;
            item.querySelector('.content-index').value = index;
        });
        
        this.updateContentArray();
    }

    /**
     * Actualizar array de contenido y campo hidden
     */
    updateContentArray() {
        console.log('🔄 Actualizando array de contenido...');
        this.contentArray = [];
        
        const items = this.container.querySelectorAll('.content-item');
        items.forEach((item, arrayIndex) => {
            const data = {
                index: arrayIndex,
                titulo: item.querySelector('.content-titulo').value || '',
                descripcion: item.querySelector('.content-descripcion').value || '',
                contenido: item.querySelector('.content-contenido').value || '',
                media: {
                    tipo: item.querySelector('.content-media-tipo').value || '',
                    url: item.querySelector('.content-media-url').value || ''
                }
            };
            this.contentArray.push(data);
        });
        
        this.updateHiddenField();
    }

    /**
     * Actualizar campo hidden
     */
    updateHiddenField() {
        if (this.hiddenField) {
            this.hiddenField.value = JSON.stringify(this.contentArray);
            console.log('💾 Campo hidden actualizado, items:', this.contentArray.length);
            
            // Disparar evento personalizado
            const event = new CustomEvent('contentUpdated', {
                detail: { data: this.contentArray, json: this.hiddenField.value }
            });
            document.dispatchEvent(event);
        }
    }

    /**
     * Cargar contenido desde JSON
     */
    loadFromJSON(jsonString) {
        console.log('📥 Cargando contenido desde JSON:', jsonString);
        
        try {
            const data = JSON.parse(jsonString || '[]');
            
            // Limpiar contenedor
            this.clear();
            
            // Cargar items
            if (Array.isArray(data) && data.length > 0) {
                data.forEach((item, index) => {
                    this.addItem({
                        ...item,
                        index: index
                    });
                });
                this.currentIndex = data.length;
            }
            
            console.log('✅ Contenido cargado:', data.length, 'items');
            
            if (window.showToast && data.length > 0) {
                window.showToast(`${data.length} elementos de contenido cargados`, 'success');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar contenido:', error);
            if (window.showToast) {
                window.showToast('Error al cargar el contenido: ' + error.message, 'error');
            }
        }
    }

    /**
     * Limpiar formulario de contenido
     */
    clear() {
        console.log('🧹 Limpiando formulario de contenido...');
        this.container.innerHTML = '';
        this.contentArray = [];
        this.currentIndex = 0;
        
        if (this.hiddenField) {
            this.hiddenField.value = '[]';
        }
        
        // Disparar evento
        const event = new CustomEvent('contentCleared', {
            detail: { message: 'Contenido limpiado' }
        });
        document.dispatchEvent(event);
    }

    /**
     * Obtener datos actuales del contenido
     */
    getData() {
        this.updateContentArray();
        return this.contentArray;
    }

    /**
     * Validar contenido antes de envío
     */
    validate() {
        const items = this.container.querySelectorAll('.content-item');
        let isValid = true;
        let errors = [];
        
        items.forEach((item, index) => {
            const titulo = item.querySelector('.content-titulo').value.trim();
            const descripcion = item.querySelector('.content-descripcion').value.trim();
            const contenido = item.querySelector('.content-contenido').value.trim();
            const mediaType = item.querySelector('.content-media-tipo').value;
            const mediaUrl = item.querySelector('.content-media-url').value.trim();
            
            if (!titulo) {
                errors.push(`Item #${index}: El título es requerido`);
                isValid = false;
            }
            
            if (!descripcion) {
                errors.push(`Item #${index}: La descripción es requerida`);
                isValid = false;
            }
            
            if (!contenido) {
                errors.push(`Item #${index}: El contenido es requerido`);
                isValid = false;
            }
            
            if (!mediaType) {
                errors.push(`Item #${index}: El tipo de media es requerido`);
                isValid = false;
            }
            
            if (!mediaUrl) {
                errors.push(`Item #${index}: La URL del media es requerida`);
                isValid = false;
            } else if (mediaType === 'image' || mediaType === 'video' || mediaType === 'audio') {
                // Validación básica de URL
                try {
                    new URL(mediaUrl);
                } catch {
                    errors.push(`Item #${index}: La URL del media no es válida`);
                    isValid = false;
                }
            }
        });
        
        return { isValid, errors };
    }

    /**
     * Obtener estadísticas del contenido
     */
    getStats() {
        this.updateContentArray();
        
        const total = this.contentArray.length;
        const images = this.contentArray.filter(item => item.media && item.media.tipo === 'image').length;
        const videos = this.contentArray.filter(item => item.media && item.media.tipo === 'video').length;
        const audios = this.contentArray.filter(item => item.media && item.media.tipo === 'audio').length;
        const documents = this.contentArray.filter(item => item.media && item.media.tipo === 'document').length;
        const jsonSize = new Blob([JSON.stringify(this.contentArray)]).size;
        
        return {
            total,
            images,
            videos,
            audios,
            documents,
            jsonSize,
            jsonSizeKB: (jsonSize / 1024).toFixed(2)
        };
    }
}

// Crear instancia global
window.contentCRUDManager = new ContentCRUDManager();

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (window.contentCRUDManager) {
        window.contentCRUDManager.init();
        
        // Funciones globales para compatibilidad con código existente
        window.jsonToContentArray = function(jsonString) {
            window.contentCRUDManager.loadFromJSON(jsonString);
        };
        
        window.clearContentForm = function() {
            window.contentCRUDManager.clear();
        };
        
        window.updateHiddenJson = function() {
            window.contentCRUDManager.updateContentArray();
        };
        
        window.getContentData = function() {
            return window.contentCRUDManager.getData();
        };
        
        window.validateContentForm = function() {
            return window.contentCRUDManager.validate();
        };
        
        console.log('✅ Funciones globales de ContentCRUDManager configuradas');
    }
});

// Exportar para uso como módulo
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ContentCRUDManager;
}