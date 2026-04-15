<x-kal::layout.guest :title="__('k::auth.password_reset.title')" :cardTitle="__('k::auth.password_reset.card_title')">

    <x-slot:cardText>{{ __('k::auth.password_reset.card_text') }}</x-slot:cardText>

    <x-kal::form method="POST" action="#" class="mt-4 lg:mt-5">
        <x-kal::form.input type="email" :label="__('k::text.input.email')" id="email" :value="old('email')" required autofocus autocomplete="email" />
        <x-kal::form.checkbox-terms />
        <x-kal::partials.form-btn>{{ __('k::auth.password_reset.btn') }}</x-kal::partials.form-btn>
    </x-kal::form>

</x-kal::layout.guest>
