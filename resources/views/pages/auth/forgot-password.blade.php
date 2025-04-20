<x-kal::layout.guest :title="__('k::text.forgot_pass.title')" :cardTitle="__('k::text.forgot_pass.card_title')">

    <x-slot:cardText>{{ __('k::text.forgot_pass.card_text') }}</x-slot:cardText>

    <x-kal::form method="POST" action="#" class="mt-4 lg:mt-5">
        <div>
            <x-kal::input.label for="email" :value="__('k::text.input.email')" />
            <x-kal::input type="email" id="email" :value="old('email')" required />
            <x-kal::input.error for="email" />
        </div>
        <x-kal::input.full.checkbox id="terms" required>
            <span class="font-light">{{ __('k::text.input.terms_part_1') }}</span>
            <x-kal::link href="#" :value="__('k::text.input.terms_part_2')" />
            <x-slot:error>
                <x-kal::input.error for="terms" />
            </x-slot:error>
        </x-kal::input.full.checkbox>
        <x-kal::form.button>{{ __('k::text.forgot_pass.btn') }}</x-kal::form.button>
    </x-kal::form>

</x-kal::layout.guest>