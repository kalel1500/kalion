<x-kal::layout.guest :title="__('k::text.password_reset.title')" :cardTitle="__('k::text.password_reset.card_title')">

    <x-slot:cardText>{{ __('k::text.password_reset.card_text') }}</x-slot:cardText>

    <x-kal::form method="POST" action="#" class="mt-4 lg:mt-5">
        <div>
            <x-kal::input.label for="email" :value="__('k::text.input.email')" />
            <x-kal::input type="email" id="email" :value="old('email')" required />
            <x-kal::input.error for="email" />
        </div>
        <x-kal::form.checkbox-terms />
        <x-kal::form.button>{{ __('k::text.password_reset.btn') }}</x-kal::form.button>
    </x-kal::form>

</x-kal::layout.guest>