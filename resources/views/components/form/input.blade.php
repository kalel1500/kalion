@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
     'type',
     'label'          => null,
     'id'             => null,
     'name'           => null,
     'value'          => '',
     'size'           => 'base',
     'containerClass' => null,
     'labelClass'     => null,
     'class'          => null,
     'rows'           => 4, // only for textarea
     'checked'        => false,
     'required'       => false,
     'disabled'       => false,
     'readonly'       => false,
])

@php
    $id   = $id   ?? $name ?? '';
    $name = $name ?? $id   ?? '';

    $checked = session()->hasOldInput() ? old($name) : $checked;
@endphp

@switch($type)
    @case('checkbox')
        {{--@props(['label', 'id' => null, 'name' => null, 'containerClass' => null, 'required' => false, 'disabled' => false])--}}
        <div @if($containerClass) class="{{ $containerClass }}" @endif>
            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    class="@twMerge('w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft', $class)"
                    {{ $attributes }}
                    @checked($checked)
                    @disabled($disabled)
                    @readonly($readonly)
                    @required($required)
                >

                <label for="{{ $id }}" class="@twMerge('ms-2 text-sm font-medium text-heading select-none', $labelClass)">
                    {{ $label ?? $slot }}
                </label>
            </div>
            <x-kal::form.error for="{{ $name }}" />
        </div>
        @break

    @case('radio')
        {{--@props(['label', 'id' => null, 'name' => null, 'containerClass' => null, 'required' => false, 'disabled' => false])--}}
        <div @if($containerClass) class="{{ $containerClass }}" @endif>
            <div class="flex items-center">
                <input
                    type="radio"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    class="@twMerge('w-4 h-4 text-neutral-primary border-default-medium bg-neutral-secondary-medium rounded-full checked:border-brand focus:ring-2 focus:outline-none focus:ring-brand-subtle border appearance-none', $class)"
                    {{ $attributes }}
                    @checked($checked)
                    @disabled($disabled)
                    @readonly($readonly)
                    @required($required)
                >

                <label for="{{ $id }}" class="@twMerge('select-none ms-2 text-sm font-medium text-heading', $labelCalss)">
                    {{ $label ?? $slot }}
                </label>
            </div>
            <x-kal::form.error for="{{ $name }}" />
        </div>
        @break

    @case('toggle')
        {{--@props(['label', 'id' => null, 'name' => null, 'required' => false, 'disabled' => false])--}}
        <div @if($containerClass) class="{{ $containerClass }}" @endif>
            <label class="inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    class="@twMerge('sr-only peer', $class)"
                    {{ $attributes }}
                    @checked($checked)
                    @disabled($disabled)
                    @readonly($readonly)
                    @required($required)
                >
                <div class="relative w-9 h-5 bg-neutral-quaternary peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-soft dark:peer-focus:ring-brand-soft rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-buffer after:content-[''] after:absolute after:top-0.5 after:inset-s-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                <span class="@twMerge('select-none ms-3 text-sm font-medium text-heading', $labelClass)">
                    {{ $label ?? $slot }}
                </span>
            </label>
            <x-kal::form.error for="{{ $name }}" />
        </div>
        @break

    @default

        @php
            $sizeClasses = [
                'small' => 'text-sm   px-2.5 py-2',
                'base'  => 'text-sm   px-3   py-2.5',
                'large' => 'text-base px-3.5 py-3',
                'extra' => 'text-base px-4   py-3.5',
            ];
            $common     = 'border rounded-base block w-full shadow-xs' . ' ' . $sizeClasses[$size];
            $normal     = 'bg-neutral-secondary-medium border-default-medium text-heading focus:ring-brand focus:border-brand placeholder:text-body';
            if (str_contains($type, 'date')) {
                $common .= ' dark:scheme-dark';
            }
            if ($disabled && !$readonly) {
                $normal = twMerge($normal, 'text-fg-disabled');
            }
            if ($type === 'textarea') {
                $normal = twMerge($normal, 'p-3.5');
            }
            $error      = 'bg-danger-soft border-danger-subtle text-fg-danger-strong focus:ring-danger focus:border-danger placeholder:text-fg-danger-strong';
            $classes    = $errors->has($name) ? twMerge($common, $class, $error) : twMerge($common, $normal, $class);

            $value = old($name, $value);
        @endphp

        <div @if($containerClass) class="{{ $containerClass }}" @endif>
            <x-kal::form.label for="{{ $id }}" :value="$label" :class="$labelClass" />
            @switch($type)
                @case('select')
                    <select
                        id="{{ $id }}"
                        name="{{ $name }}"
                        class="{{ $classes }}"
                        {{ $attributes }}
                        @disabled($disabled)
                        @readonly($readonly)
                        @required($required)
                    >
                        {{ $slot }}
                    </select>
                    @break

                @case('textarea')
                    <textarea
                        id="{{ $id }}"
                        name="{{ $name }}"
                        rows="{{ $rows }}"
                        class="{{ $classes }}"
                        {{ $attributes }}
                        @disabled($disabled)
                        @readonly($readonly)
                        @required($required)
                    >{{ $value }}</textarea>
                    @break

                @default
                    <input
                        type="{{ $type }}"
                        id="{{ $id }}"
                        name="{{ $name }}"
                        value="{{ $value }}"
                        class="{{ $classes }}"
                        {{ $attributes }}
                        @disabled($disabled)
                        @readonly($readonly)
                        @required($required)
                    />
            @endswitch
            <x-kal::form.error for="{{ $name }}" />
        </div>

@endswitch
