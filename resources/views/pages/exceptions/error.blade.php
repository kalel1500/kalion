@php /** @var \Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto $context */ @endphp

<x-kal::layout.minimal
    :title="$context->title"
    :code="$context->statusCode"
    :message="$context->message"
>
    @if($context->showLogout)
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="cursor-pointer hover:underline">Logout</button>
        </form>
    @endif
</x-kal::layout.minimal>

{{--
<div>
    <span>Variables:</span>
    <ul class="m-0">
        @foreach($data['data'] as $key => $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
</div>
--}}
