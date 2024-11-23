@props(['strokeWidth' => 1.5])

<div {{ $attributes->class('rounded-full bg-gray-800 dark:bg-white') }}>
    <svg class="text-white dark:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
        <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
    </svg>
</div>
@if($attributes->has('flowbite'))

@elseif($attributes->has('outline'))

@else

@endif