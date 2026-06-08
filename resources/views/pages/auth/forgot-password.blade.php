<x-kal::layout.guest :title="__('k::auth.forgot_password.title')" :cardTitle="__('k::auth.forgot_password.card_title')">

    <x-slot:cardText>{{ __('k::auth.forgot_password.card_text') }}</x-slot:cardText>

    <x-kal::form method="POST" action="{{ route('password.email') }}" class="mt-4 lg:mt-5">
        <x-kal::form.input type="email" :label="__('k::text.input.email')" id="email" :value="old('email')" required autofocus autocomplete="email" />
        <x-kal::partials.form-btn>{{ __('k::auth.forgot_password.btn') }}</x-kal::partials.form-btn>
    </x-kal::form>

</x-kal::layout.guest>
