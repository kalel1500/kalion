@php /** @var \Src\Dashboard\Domain\Objects\DataObjects\DashboardDataDto $data */ @endphp
@php /** @var \Src\Shared\Domain\Objects\Entities\PostEntity $post */ @endphp
@php /** @var \Src\Shared\Domain\Objects\Entities\TagEntity $tag */ @endphp

<x-kal::layout.app title="{{ config('app.name')}} - Dashboard">

    <div class="flex items-center gap-3">
        <x-kal::heading type="h2">Posts</x-kal::heading>

        <x-kal::badge color="blue">{{ $data->count_posts }}</x-kal::badge>

        <x-kal::input.select id="selectTags" class="w-auto">
            <option value="">Filtrar por tags</option>
            @foreach($data->tags as $tag)
                <option value="{{ $tag->code->value() }}" @selected($tag->name->value() === $data->selected_tag)>{{ $tag->name->value() }}</option>
            @endforeach
        </x-kal::input.select>
    </div>

    <div class="flex flex-wrap justify-around xs:grid xs:grid-cols-[repeat(auto-fit,minmax(24rem,1fr))] gap-6">
        @foreach($data->posts as $post)

            <div class="w-full p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">

                <a href="#">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white hover:text-gray-700 dark:hover:text-gray-300">{{ $post->title->value() }}</h5>
                </a>

                <x-kal::text class="mb-3">{{ $post->content->value() }}</x-kal::text>

                <a href="{{ route('post', $post->slug->value()) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Read more
                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                    </svg>
                </a>

                <div>
                    <x-kal::text class="mt-3">{{ $post->created_at->formatToSpainDatetime() }}</x-kal::text>

                    @foreach($post->tags() as $tag)
                        <x-kal::link href="{{ route('dashboard', ['tag' => $tag->code->value()]) }}">
                            <x-kal::badge color="yellow">{{ $tag->name }}</x-kal::badge>
                        </x-kal::link>
                    @endforeach
                </div>
            </div>

        @endforeach
    </div>

</x-kal::layout.app>
