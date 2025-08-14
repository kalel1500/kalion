@php /** @var \Thehouseofel\Kalion\Domain\Objects\DataObjects\ExceptionContextDto $context */ @endphp
@extends('kal::pages.exceptions.minimal')

@section('title', $context->title)
@section('code', $context->statusCode)
@section('message', $context->message)


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
