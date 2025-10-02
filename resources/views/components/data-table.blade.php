@props([
    'containerId' => 'TableContainer',
    'loadingText' => 'Cargando datos...'
])

<div class="content-card">
    <div id="{{ $containerId }}" style="margin-top: 20px;">
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ $loadingText }}</span>
            </div>
            <p class="mt-2 text-muted">{{ $loadingText }}</p>
        </div>
    </div>
</div>