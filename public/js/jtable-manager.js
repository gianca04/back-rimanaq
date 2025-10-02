/**
 * jTable Manager - Configuración y utilidades para jTable
 */
window.JTableManager = (function() {
    'use strict';

    // Configuración base para jTable
    const defaultConfig = {
        paging: true,
        pageSize: 15,
        sorting: true,
        selectOnLoad: false
    };

    // Estilos comunes para campos
    const fieldStyles = {
        id: {
            key: true,
            create: false,
            edit: false,
            list: true,
            title: 'ID',
            width: '5%'
        },
        name: {
            title: 'Nombre',
            width: '25%',
            inputClass: 'form-control',
            inputTitle: 'Nombre (requerido)'
        },
        description: {
            title: 'Descripción',
            width: '30%',
            type: 'textarea',
            inputClass: 'form-control',
            inputTitle: 'Descripción (requerido)'
        }
    };

    // Configuraciones específicas por tipo
    const configs = {
        lessons: {
            getFields: function(courseId = null, coursesOptions = {}) {
                return {
                    ...fieldStyles.id,
                    course_id: courseId ? {
                        type: 'hidden',
                        defaultValue: function() { return courseId; },
                        create: true,
                        edit: false,
                        list: false
                    } : {
                        title: 'Curso',
                        width: '15%',
                        type: 'option',
                        options: coursesOptions,
                        display: function(data) {
                            return coursesOptions[data.record.course_id] || 'N/A';
                        }
                    },
                    ...fieldStyles.name,
                    ...fieldStyles.description,
                    level_number: {
                        title: 'Nivel',
                        width: '8%',
                        type: 'number',
                        inputClass: 'form-control',
                        defaultValue: 1,
                        inputTitle: 'Número del nivel (1-100)'
                    },
                    difficulty: {
                        title: 'Dificultad',
                        width: '12%',
                        type: 'option',
                        options: {
                            'fácil': 'Fácil',
                            'intermedio': 'Intermedio',
                            'difícil': 'Difícil'
                        },
                        defaultValue: 'fácil',
                        inputClass: 'form-select',
                        display: function(data) {
                            return App.Utils.formatDifficulty(data.record.difficulty);
                        }
                    },
                    time_minutes: {
                        title: 'Duración',
                        width: '10%',
                        type: 'number',
                        inputClass: 'form-control',
                        defaultValue: 30,
                        inputTitle: 'Duración en minutos (5-600)',
                        display: function(data) {
                            return App.Utils.formatDuration(data.record.time_minutes);
                        }
                    },
                    Actions: {
                        title: 'Gestos',
                        width: '8%',
                        sorting: false,
                        edit: false,
                        create: false,
                        display: function(data) {
                            return `<button type="button" class="btn btn-sm btn-success" onclick="viewLessonGestures(${data.record.id}, '${data.record.name.replace(/'/g, "\\'")}')">
                                        <i class="fas fa-hand-paper me-1"></i>Ver
                                    </button>`;
                        }
                    }
                };
            },

            getActions: function(endpoint, courseId = null) {
                return {
                    listAction: function(postData) {
                        const url = courseId ? 
                            `/api/courses/${courseId}/lessons` : 
                            App.Config.api.endpoints.lessons;
                            
                        return App.API.request(url)
                            .then(function(response) {
                                return {
                                    Result: 'OK',
                                    Records: response.data || [],
                                    TotalRecordCount: (response.data || []).length
                                };
                            })
                            .catch(function(xhr) {
                                const message = App.API.handleError(xhr, 'loading lessons');
                                return {
                                    Result: 'ERROR',
                                    Message: message
                                };
                            });
                    },

                    createAction: function(postData) {
                        console.log('=== CREATE ACTION ===');
                        
                        const formData = App.Utils.parseFormData(postData);
                        console.log('Parsed form data:', formData);

                        // Si hay courseId, asegurar que esté en los datos
                        if (courseId) {
                            formData.course_id = parseInt(courseId);
                        }

                        // Validar campos requeridos
                        const errors = App.Utils.validateRequired(formData, ['name', 'description']);
                        if (errors.length > 0) {
                            App.Utils.showAlert(errors.join('\n'), 'danger');
                            return Promise.resolve({
                                Result: 'ERROR',
                                Message: errors.join(', ')
                            });
                        }

                        // Convertir tipos
                        formData.level_number = parseInt(formData.level_number) || 1;
                        formData.time_minutes = parseInt(formData.time_minutes) || 30;
                        if (formData.course_id) {
                            formData.course_id = parseInt(formData.course_id);
                        }

                        console.log('Final data to send:', formData);

                        return App.API.request(App.Config.api.endpoints.lessons, {
                            type: 'POST',
                            data: formData,
                            contentType: 'application/x-www-form-urlencoded'
                        })
                        .then(function(response) {
                            App.Utils.showAlert('Lección creada exitosamente', 'success');
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        })
                        .catch(function(xhr) {
                            const message = App.API.handleError(xhr, 'creating lesson');
                            return {
                                Result: 'ERROR',
                                Message: message
                            };
                        });
                    },

                    updateAction: function(postData) {
                        const formData = App.Utils.parseFormData(postData);
                        
                        if (courseId && !formData.course_id) {
                            formData.course_id = parseInt(courseId);
                        }

                        // Convertir tipos
                        if (formData.level_number) formData.level_number = parseInt(formData.level_number);
                        if (formData.time_minutes) formData.time_minutes = parseInt(formData.time_minutes);
                        if (formData.course_id) formData.course_id = parseInt(formData.course_id);

                        return App.API.request(`${App.Config.api.endpoints.lessons}/${formData.id}`, {
                            type: 'PUT',
                            data: formData,
                            contentType: 'application/x-www-form-urlencoded'
                        })
                        .then(function(response) {
                            App.Utils.showAlert('Lección actualizada exitosamente', 'success');
                            return {
                                Result: 'OK',
                                Record: response.data
                            };
                        })
                        .catch(function(xhr) {
                            const message = App.API.handleError(xhr, 'updating lesson');
                            return {
                                Result: 'ERROR',
                                Message: message
                            };
                        });
                    },

                    deleteAction: function(postData) {
                        return App.API.request(`${App.Config.api.endpoints.lessons}/${postData.id}`, {
                            type: 'DELETE'
                        })
                        .then(function(response) {
                            App.Utils.showAlert('Lección eliminada exitosamente', 'success');
                            return {
                                Result: 'OK'
                            };
                        })
                        .catch(function(xhr) {
                            const message = App.API.handleError(xhr, 'deleting lesson');
                            return {
                                Result: 'ERROR',
                                Message: message
                            };
                        });
                    }
                };
            }
        }
    };

    // Función para crear configuración completa
    function createConfig(type, options = {}) {
        const config = { ...defaultConfig };
        
        if (configs[type]) {
            config.actions = configs[type].getActions(options.endpoint, options.courseId);
            config.fields = configs[type].getFields(options.courseId, options.coursesOptions);
        }
        
        // Agregar configuraciones específicas
        if (options.title) config.title = options.title;
        if (options.defaultSorting) config.defaultSorting = options.defaultSorting;
        
        return config;
    }

    // Función para inicializar jTable
    function initialize(selector, type, options = {}) {
        // Esperar a que jTable esté disponible
        function waitForJTable() {
            if (typeof $.fn.jtable !== 'undefined') {
                console.log('✅ jTable available, initializing...');
                
                const config = createConfig(type, options);
                console.log('jTable config:', config);
                
                $(selector).jtable(config);
                $(selector).jtable('load');
                
                // Event handlers comunes
                setupEventHandlers(selector, options);
                
            } else {
                console.log('⏳ Waiting for jTable...');
                setTimeout(waitForJTable, 500);
            }
        }
        
        waitForJTable();
    }

    // Event handlers comunes
    function setupEventHandlers(selector, options) {
        $(selector).on('formCreated', function(event, data) {
            console.log('📝 Form created:', data.formType);
            
            // Aplicar estilos Bootstrap
            setTimeout(function() {
                data.form.find('input, select, textarea').addClass('form-control');
                data.form.find('select').removeClass('form-control').addClass('form-select');
                
                // Debug de campos
                console.log('Form fields:');
                data.form.find('input, select, textarea').each(function(i, field) {
                    console.log(`  ${field.name}: "${field.value}" (${field.type})`);
                });
            }, 50);
        });

        // Event handlers personalizados
        if (options.onRecordAdded) {
            $(selector).on('recordAdded', options.onRecordAdded);
        }
        if (options.onRecordUpdated) {
            $(selector).on('recordUpdated', options.onRecordUpdated);
        }
        if (options.onRecordDeleted) {
            $(selector).on('recordDeleted', options.onRecordDeleted);
        }
    }

    // Cargar jTable dinámicamente
    function loadJTable() {
        return new Promise((resolve, reject) => {
            if (typeof $.fn.jtable !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/jquery.jtable.min.js';
            script.onload = () => {
                console.log('✅ jTable loaded successfully');
                
                // Cargar CSS también
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdnjs.cloudflare.com/ajax/libs/jtable/2.5.0/themes/lightcolor/blue/jtable.min.css';
                document.head.appendChild(link);
                
                resolve();
            };
            script.onerror = () => {
                console.error('❌ Failed to load jTable');
                reject(new Error('Failed to load jTable'));
            };
            document.head.appendChild(script);
        });
    }

    // API pública
    return {
        initialize: initialize,
        createConfig: createConfig,
        loadJTable: loadJTable,
        configs: configs
    };
})();