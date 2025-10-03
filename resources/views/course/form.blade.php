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
		<label for="color" class="form-label">Color (hex)</label>
		<input type="text" class="form-control" id="color" name="color" maxlength="7" placeholder="#123ABC">
	</div>
	<button type="submit" class="btn btn-success w-100">Guardar</button>
</form>
