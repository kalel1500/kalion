@props(['editId', 'cancelId', 'addId'])

<div class="flex gap-3">
    <x-kal::button id="{{ $editId }}" class="px-2 py-1" variant="warning"><x-kal::icon.pencil-square class="size-5"/></x-kal::button>
    <x-kal::button id="{{ $cancelId }}" class="px-2 py-1 hidden" variant="secondary"><x-kal::icon.x-circle class="size-5"/></x-kal::button>
    <x-kal::button id="{{ $addId }}" class="px-2 py-1" variant="brand"><x-kal::icon.plus-circle class="size-5"/></x-kal::button>
</div>
