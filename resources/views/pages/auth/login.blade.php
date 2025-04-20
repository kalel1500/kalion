@use(Thehouseofel\Kalion\Infrastructure\Services\Kalion)
@php($field = Kalion::getLoginFieldData())

<x-kal::layout.guest :title="__('k::text.login.title')" :cardTitle="__('k::text.login.card_title')">

    <x-kal::form method="POST" action="{{ route('login') }}">
        <div>
            <x-kal::input.label :for="$field->name" :value="__($field->label)"/>
            <x-kal::input :type="$field->type" :id="$field->name" class="text-base" :value="old($field->name)" required/>
            <x-kal::input.error :for="$field->name" />
        </div>
        <div>
            <x-kal::input.label for="password" :value="__('k::text.input.password')" />
            <x-kal::input type="password" id="password" class="text-base" required />
        </div>
        <div class="flex items-center justify-between">
            <x-kal::input.full.checkbox id="remember" :labelText="__('k::text.input.remember_me')" />
            <x-kal::link href="{{ route('password.reset') }}" class="text-sm" :value="__('k::text.login.password_reset')" />
        </div>

        <x-kal::form.button>{{ __('k::text.login.btn') }}</x-kal::form.button>

        <x-kal::form.question-link :value="__('k::text.login.question')" :link="__('k::text.login.question_link')" href="{{ route('register') }}" />
    </x-kal::form>

</x-kal::layout.guest>