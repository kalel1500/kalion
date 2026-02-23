@php /** @var \Src\Tags\Domain\Objects\DataObjects\ViewTagsDto $data */ @endphp
@php /** @var \Src\Shared\Domain\Objects\Entities\TagTypeEntity $tagType */ @endphp

<x-kal::layout.app title="{{ config('app.name')}} - Tags">

    <div class="flex items-center gap-3">
        <x-kal::heading type="h2">Tags</x-kal::heading>
    </div>

    <x-kal::section class="w-full justify-self-center">
        <div class="md:flex md:justify-between md:items-center">
            <x-kal::tab class="flex-1 mb-3 md:mb-0">
                <x-kal::tab.item :active="is_null($data->currentTagType)" :href="route('tags')">Todos</x-kal::tab.item>
                @foreach($data->tagTypes as $tagType)
                    <x-kal::tab.item :active="$data->currentTagType?->id->value === $tagType->id->value" :href="route('tags', $tagType->code->value)">{{ $tagType->name }}</x-kal::tab.item>
                @endforeach
            </x-kal::tab>

            <x-kal::tabulator.buttons
                editId="btn-tag-edit"
                cancelId="btn-tag-cancel"
                addId="btn-tag-add"
            />
        </div>

        <div id="table-tags" class="mt-4"></div>
    </x-kal::section>

</x-kal::layout.app>
