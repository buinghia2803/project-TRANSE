@php
    $star = floor($star * 2) / 2;
@endphp

<div class="d-flex align-items-center {{ $class ?? 'justify-content-end' }}">
    <span class="mr-1">({{ $star }})</span>

    @for($i = 0; $i < floor($star); $i++)
        <i class="fa fa-star text-yellow"></i>
    @endfor

    @if($star - floor($star) == 0.5)
        <i class="fa fa-star-half-alt text-yellow"></i>
    @endif

    @for($i = 0; $i < 5 - ceil($star); $i++)
        <i class="far fa-star text-yellow"></i>
    @endfor
</div>
