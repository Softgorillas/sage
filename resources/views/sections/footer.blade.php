<footer class="flex items-start justify-between">
    <a href="{{ home_url('/') }}" aria-label="{{ __('Home', 'gorilla') }}">
        {!! $siteName !!}
    </a>

    @if ($footer_navigation->isNotEmpty())
        <nav aria-label="{{ __('Footer Navigation', 'gorilla') }}">
            <ul class="flex items-center space-x-4">
                @foreach ($footer_navigation->all() as $item)
                    <li class="{{ $item->classes }} {{ $item->active ? 'current-item' : '' }}">
                        <a href="{{ $item->url }}">
                            {{ $item->label }}
                        </a>
                        @if ($item->children)
                            <ul>
                                @foreach ($item->children as $child)
                                    <li class="{{ $child->classes }}{{ $child->active ? ' current-item' : '' }}">
                                        <a href="{{ $child->url }}">
                                            {{ $child->label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif
</footer>
