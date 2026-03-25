{{--@php($errors = session('errors') ?: new \Illuminate\Support\ViewErrorBag)--}}
@if($errors->any())
    <x-kal::alert dismiss border icon list variant="danger" title="{{ __('Several errors have been detected') }}:">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </x-kal::alert>
@endif

@if(session()->has('success'))
    <x-kal::alert dismiss variant="success">{{ session()->get('success') }}</x-kal::alert>
@endif

@if(session()->has('error'))
    <x-kal::alert dismiss variant="danger">{{ session()->get('error') }}</x-kal::alert>
@endif

@if(session()->has('severalErrors'))
    <x-kal::alert dismiss border icon list variant="danger">
        @foreach(session()->get('severalErrors') as $key => $error)
            @if($key === 0)
                <x-slot:title>{{ $error }}</x-slot:title>
            @else
                <li>{{ $error }}</li>
            @endif
        @endforeach
    </x-kal::alert>
@endif
