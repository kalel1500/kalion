@if (str_contains_html($icon))
    {!! $icon !!}
@else
    @if (str_contains($icon, ';'))
        @php([$icon, $class] = explode(';', $icon))
        <x-dynamic-component :component="$icon" :class="$class" {{ $attributes }} />
    @else
        <x-dynamic-component :component="$icon" {{ $attributes }} />
    @endif
@endif