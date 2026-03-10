@if (str_contains_html($icon))
    {!! $icon !!}
@else
    @if (str_contains($icon, ';'))
        @php([$icon, $class] = explode(';', $icon))
        <x-dynamic-component :component="$icon" :class="$class" />
    @else
        <x-dynamic-component :component="$icon" />
    @endif
@endif
