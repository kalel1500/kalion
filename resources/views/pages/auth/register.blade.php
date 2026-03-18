<x-kal::layout.guest :title="__('k::auth.register.title')" :cardTitle="__('k::auth.register.card_title')">

    <x-kal::form method="POST" action="{{ route('register') }}">

        <!-- Name -->
        <div>
            <x-kal::form.input type="text" :label="__('k::text.input.name')" id="name" :value="old('name')" required autofocus autocomplete="name" />
        </div>

        <!-- Email Address -->
        <div>
            <x-kal::form.input type="email" :label="__('k::text.input.email')" id="email" :value="old('email')" required autocomplete="email" />
        </div>

        <!-- Password -->
        <div>
            <x-kal::form.input type="password" :label="__('k::text.input.password')" id="password" required autocomplete="new-password" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-kal::form.input type="password" :label="__('k::text.input.password_confirmation')" id="password_confirmation" required autocomplete="new-password" />
        </div>

        <!-- Terms -->
        <x-kal::form.checkbox-terms />

        <!-- Submit btn -->
        <x-kal::form.button>{{ __('k::auth.register.btn') }}</x-kal::form.button>

        <!-- Login link -->
        <x-kal::form.question-link :value="__('k::auth.register.question')" :link="__('k::auth.register.question_link')" href="{{ route('login') }}" />
    </x-kal::form>

</x-kal::layout.guest>
