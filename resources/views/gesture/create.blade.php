@extends('layouts.app')

@section('title', 'Crear gesto')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Crear Nuevo Gesto</h3>
        <a href="{{ route('web.gestures.index') }}" class="btn btn-secondary">Volver a Gestos</a>
    </div>
</div>
@endsection