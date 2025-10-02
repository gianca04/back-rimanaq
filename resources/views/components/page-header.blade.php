@props([
    'title' => 'Página',
    'subtitle' => null,
    'icon' => null,
    'stats' => null
])

<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="{{ isset($stats) ? 'col-md-8' : 'col-12' }}">
                <h1>
                    @if($icon)
                        <i class="{{ $icon }} me-3"></i>
                    @endif
                    {{ $title }}
                </h1>
                @if($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($stats))
                <div class="col-md-4">
                    <div class="stats-card text-dark">
                        <h3 id="{{ $stats['id'] ?? 'statValue' }}">{{ $stats['value'] ?? '0' }}</h3>
                        <p>{{ $stats['label'] ?? 'Total' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>