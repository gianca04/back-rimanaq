{{-- resources/views/components/head.blade.php --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Rimanaq')</title>

{{-- CSS Files --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

{{-- Additional head content from specific pages --}}
@yield('head')

{{-- Styles from specific pages --}}
@yield('styles')

{{-- Favicon --}}
<link rel="icon" href="/favicon.ico" type="image/x-icon">