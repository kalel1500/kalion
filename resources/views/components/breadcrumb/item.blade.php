@props(['href' => null, 'active' => false])

<li class="group">
    {{-- El separador SVG como pseudo-elemento o elemento condicional --}}
    <div class="flex items-center">
        {{--
           Este SVG solo se muestra si NO es el primer <li> de la lista.
           Usamos "group-first:hidden" para que el primer item lo oculte.
        --}}
        <svg class="w-3.5 h-3.5 rtl:rotate-180 text-body group-first:hidden me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
        </svg>

        @if($href && !$active)
            <a href="{{ $href }}" class="inline-flex items-center text-sm font-medium text-body hover:text-fg-brand">
                {{ $slot }}
            </a>
        @else
            <span class="inline-flex items-center text-sm font-medium text-body-subtle">
                {{ $slot }}
            </span>
        @endif
    </div>
</li>
