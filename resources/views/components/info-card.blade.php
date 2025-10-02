@props([
    'title' => null,
    'class' => 'course-info'
])

<div class="content-card {{ $class }}">
    @if($title)
        <h4>{{ $title }}</h4>
    @endif
    {{ $slot }}
</div>