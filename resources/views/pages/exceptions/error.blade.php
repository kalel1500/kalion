@php /** @var \Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto $context */ @endphp
@extends('kal::pages.exceptions.minimal')

@section('title', __($context->title))
@section('code', $context->statusCode)
@section('message', $context->message)
@section('html')
    @if($context->showLogout)
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="cursor-pointer hover:underline">Logout</button>
        </form>
    @endif
@endsection

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
