@php($field = kauth()->getLoginFieldData())

<x-kal::layout.guest :title="__('k::auth.login.title')" :cardTitle="__('k::auth.login.card_title')">

    <x-kal::form method="POST" action="{{ route('login') }}">
        <!-- Email Address -->
        <div>
            <x-kal::form.label :for="$field->name" :value="__($field->label)"/>
            <x-kal::form.input :type="$field->type" :id="$field->name" class="text-base" :value="old($field->name)" required autofocus autocomplete="username" />
        </div>

        <!-- Password -->
        <div>
            <x-kal::form.label for="password" :value="__('k::text.input.password')" />
            <x-kal::form.input type="password" id="password" class="text-base" required autocomplete="current-password" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <x-kal::form.checkbox id="remember" :label="__('k::text.input.remember_me')" />
            @if(! config('kalion.auth.disable_password_reset'))
                <x-kal::link href="{{ route('password.reset') }}" class="text-sm" :value="__('k::auth.login.password_reset')" />
            @endif
        </div>

        <!-- Submit btn -->
        <x-kal::form.button>{{ __('k::auth.login.btn') }}</x-kal::form.button>

        <!-- Register link -->
        @if(! config('kalion.auth.disable_register'))
            <x-kal::form.question-link :value="__('k::auth.login.question')" :link="__('k::auth.login.question_link')" href="{{ route('register') }}" />
        @endif
    </x-kal::form>

</x-kal::layout.guest>
