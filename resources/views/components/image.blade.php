<picture>
    @php
        $breakpointsReverse = array_reverse($breakpoints);
        $loading = $eagerLoading ? 'eager' : 'lazy';
    @endphp

    @if (!empty($imageElements))
        @foreach ($breakpointsReverse as $key => $size)
            <source media="(min-width:{!! $size !!}px)" srcset="{!! $imageElements[$key]['srcWebp'] !!}" type="image/webp">
            <source media="(min-width:{!! $size !!}px)" srcset=" {!! $imageElements[$key]['src'] !!} ">
        @endforeach

        <img decoding="async" @if ($loading === 'eager') fetchpriority="high" @endif src="{!! $imageElements[0]['src'] !!}"
            alt="{!! $alt !!}" width="{!! $imageElements[0]['width'] !!}" height="{!! $imageElements[0]['height'] !!}"
            loading="{!! $loading !!}">
    @endif
</picture>
