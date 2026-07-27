@use('Thehouseofel\Kalion\Core\Domain\Support\Str')
@props(['icon' => '', 'class' => ''])

@if (Str::containsHtml($icon))
    {!! $icon !!}
@else
    @if (str_contains($icon, ';'))
        @php([$icon, $iconClass] = explode(';', $icon))
        <x-dynamic-component :component="$icon" :class="twMerge($class, $iconClass)" />
    @else
        <x-dynamic-component :component="$icon" :class="$class" />
    @endif
@endif
