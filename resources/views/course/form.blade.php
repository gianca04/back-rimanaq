<form id="courseForm">
	<div class="mb-3">
		<label for="name" class="form-label">Nombre</label>
		<input type="text" class="form-control" id="name" name="name" required maxlength="255">
	</div>
	<div class="mb-3">
		<label for="description" class="form-label">Descripción</label>
		<textarea class="form-control" id="description" name="description" required></textarea>
	</div>
	<div class="mb-3">
		<label for="image_path" class="form-label">Imagen (URL)</label>
		<input type="text" class="form-control" id="image_path" name="image_path" maxlength="255">
	</div>
	<div class="mb-3">
		<label for="color" class="form-label">Color</label>
		<div class="d-flex gap-2 align-items-center">
			<input type="color" class="form-control form-control-color" id="colorPicker" value="#6366f1" style="width: 60px; height: 38px;">
			<input type="text" class="form-control" id="color" name="color" maxlength="7" placeholder="#6366f1" pattern="^#[0-9A-Fa-f]{6}$">
		</div>
		<small class="form-text text-muted">Usa el selector de color o escribe el código hex</small>
	</div>
	<button type="submit" class="btn btn-success w-100">Guardar</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('colorPicker');
    const colorInput = document.getElementById('color');

    // Sincronizar selector de color con input de texto
    colorPicker.addEventListener('input', function() {
        colorInput.value = this.value.toUpperCase();
    });

    // Sincronizar input de texto con selector de color
    colorInput.addEventListener('input', function() {
        const hexColor = this.value;
        // Validar formato hex válido
        if (/^#[0-9A-Fa-f]{6}$/.test(hexColor)) {
            colorPicker.value = hexColor;
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (hexColor.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Inicializar con valor por defecto
    colorInput.value = colorPicker.value;
});
</script>
