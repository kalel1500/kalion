<x-kal::layout.guest :title="__('k::auth.register.title')" :cardTitle="__('k::auth.register.card_title')">

    <x-kal::form method="POST" action="{{ route('register') }}">

        <!-- Name -->
        <div>
            <x-kal::input.label for="name" :value="__('k::text.input.name')" />
            <x-kal::input type="text" id="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-kal::input.error for="name" />
        </div>

        <!-- Email Address -->
        <div>
            <x-kal::input.label for="email" :value="__('k::text.input.email')" />
            <x-kal::input type="email" id="email" :value="old('email')" required autocomplete="email" />
            <x-kal::input.error for="email" />
        </div>

        <!-- Password -->
        <div>
            <x-kal::input.label for="password" :value="__('k::text.input.password')" />
            <x-kal::input type="password" id="password" required autocomplete="new-password" />
            <x-kal::input.error for="password" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-kal::input.label for="password_confirmation" :value="__('k::text.input.password_confirmation')" />
            <x-kal::input type="password" id="password_confirmation" required autocomplete="new-password" />
            <x-kal::input.error for="password_confirmation" />
        </div>

        <!-- Terms -->
        <x-kal::form.checkbox-terms />

        <!-- Submit btn -->
        <x-kal::form.button>{{ __('k::auth.register.btn') }}</x-kal::form.button>

        <!-- Login link -->
        <x-kal::form.question-link :value="__('k::auth.register.question')" :link="__('k::auth.register.question_link')" href="{{ route('login') }}" />
    </x-kal::form>

</x-kal::layout.guest>