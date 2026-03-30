@props([
    'text'        => null,
    'variant'     => 'brand',
    'bordered'    => false,
    'large'       => false,
    'pill'        => false,
    'href'        => null,
    'icon'        => null,      // Puede ser un slot o una variable con el SVG
    'dot'         => false,     // Si es true, muestra el círculo
    'loader'      => false,     // Si es true, muestra el spinner
    'dismissible' => false,     // Si es true, añade el botón de cerrar
    'id'          => 'badge-' . uniqid(), // ID único para el dismiss
])

@php
    $hasContent = !empty($text) || !empty($slot->toHtml());
    $isOnlyIcon = ($icon || $loader) && !$hasContent;

    // 1. Configuración de Colores y Mapas
    $colorMap = [
        'brand'       => ['bg' => 'bg-brand-softer',                'text' => 'text-fg-brand-strong',   'dot' => 'bg-fg-brand-strong',      'hover' => 'hover:bg-brand-soft',               'spinner' => 'text-fg-brand',           'spinnerFill' => '#1C64F2'],
        'alternative' => ['bg' => 'bg-neutral-primary-soft',        'text' => 'text-heading',           'dot' => 'bg-heading',              'hover' => 'hover:bg-neutral-secondary-medium', 'spinner' => 'text-neutral-tertiary',   'spinnerFill' => '#6A7282'],
        'gray'        => ['bg' => 'bg-neutral-secondary-medium',    'text' => 'text-heading',           'dot' => 'bg-heading',              'hover' => 'hover:bg-neutral-tertiary-medium',  'spinner' => 'text-neutral-quaternary', 'spinnerFill' => '#6A7282'],
        'danger'      => ['bg' => 'bg-danger-soft',                 'text' => 'text-fg-danger-strong',  'dot' => 'bg-fg-danger-strong',     'hover' => 'hover:bg-danger-medium',            'spinner' => 'text-danger-medium',      'spinnerFill' => '#C70036'],
        'success'     => ['bg' => 'bg-success-soft',                'text' => 'text-fg-success-strong', 'dot' => 'bg-fg-success-strong',    'hover' => 'hover:bg-success-medium',           'spinner' => 'text-success-medium',     'spinnerFill' => '#009966'],
        'warning'     => ['bg' => 'bg-warning-soft',                'text' => 'text-fg-warning',        'dot' => 'bg-fg-warning',           'hover' => 'hover:bg-warning-medium',           'spinner' => 'text-warning-medium',     'spinnerFill' => '#D03801'],
    ];

    $current = $colorMap[$variant];

    // 2. Lógica de Dimensiones
    if ($isOnlyIcon) {
        $sizeClasses = $large ? 'h-6 w-6' : 'h-5 w-5';
        $paddingClasses = 'justify-center';
        $roundClasses = 'rounded-full'; // Siempre circular si es solo icono
    } else {
        $sizeClasses = $large ? 'text-sm' : 'text-xs';
        $paddingClasses = $large ? 'px-2 py-1' : ($dismissible ? 'ps-1.5 pe-0.5 py-0.5' : 'px-1.5 py-0.5');
        $roundClasses = $pill ? 'rounded-full' : 'rounded';
    }

    // 3. Lógica de Bordes vs Rings (La excepción que comentamos)
    $borderClasses = '';
    if ($bordered) {
        if ($large) {
            $ringMap = ['brand'=>'ring-brand-subtle','alternative'=>'ring-default','gray'=>'ring-default-medium','danger'=>'ring-danger-subtle','success'=>'ring-success-subtle','warning'=>'ring-warning-subtle'];
            $borderClasses = 'ring-1 ring-inset ' . $ringMap[$variant];
        } else {
            $strokeMap = ['brand'=>'border-brand-subtle','alternative'=>'border-default','gray'=>'border-default-medium','danger'=>'border-danger-subtle','success'=>'border-success-subtle','warning'=>'border-warning-subtle'];
            $borderClasses = 'border ' . $strokeMap[$variant];
        }
    }

    // 4. Construcción final de clases
    $classes = "inline-flex items-center font-medium {$current['bg']} {$current['text']} {$sizeClasses} {$paddingClasses} {$roundClasses} {$borderClasses}";
    if ($href) $classes .= " {$current['hover']}";
    if ($loader || $dismissible) $classes .= " gap-1";

    $tag = $href ? 'a' : 'span';
@endphp

<{{ $tag }} {{ $href ? "href=$href" : '' }} id="{{ $id }}" {{ $attributes->twMerge($classes) }}>

{{-- Lógica de Icono / Dot / Loader --}}
@if($loader)
    <svg aria-hidden="true" role="status" class="{{ $large ? 'w-3.5 h-3.5' : 'w-3 h-3' }} animate-spin {{ $current['spinner'] }}" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="{{ $current['spinnerFill'] }}"/></svg>
@elseif($dot)
    <span class="h-1.5 w-1.5 {{ $current['dot'] }} rounded-full {{ $hasContent ? 'me-1' : '' }}"></span>
@elseif($icon)
    <span class="{{ $large ? 'w-3.5 h-3.5 me-1.5' : 'w-3 h-3 me-1' }} inline-flex items-center">
        {{ $icon }}
    </span>
@endif

{{-- Texto --}}
@if($hasContent)
    <span>{{ $text ?? $slot }}</span>
@endif

{{-- Botón Dismiss --}}
@if($dismissible)
    <button type="button" class="inline-flex items-center p-0.5 text-sm bg-transparent rounded-xs {{ $current['hover'] }}" data-dismiss-target="#{{ $id }}" aria-label="Remove">
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
        <span class="sr-only">Remove badge</span>
    </button>
@endif

</{{ $tag }}>
