@php /** @var \Src\Shared\Domain\Objects\Entities\PostEntity $post */ @endphp

<x-kal::layout.app title="{{ config('app.name')}} - Dashboard">

    <x-kal::section class="w-1/2 justify-self-center">
        <x-kal::heading type="h2">{{ $post->title }}</x-kal::heading>

        <x-kal::text>{{ $post->content }}</x-kal::text>

    </x-kal::section>

</x-kal::layout.app>
