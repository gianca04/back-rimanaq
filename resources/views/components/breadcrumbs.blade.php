<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($breadcrumbs as $breadcrumb)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    @if(isset($breadcrumb['icon']))
                        <i class="{{ $breadcrumb['icon'] }} me-1"></i>
                    @endif
                    {{ $breadcrumb['title'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] }}">
                        @if(isset($breadcrumb['icon']))
                            <i class="{{ $breadcrumb['icon'] }} me-1"></i>
                        @endif
                        {{ $breadcrumb['title'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>