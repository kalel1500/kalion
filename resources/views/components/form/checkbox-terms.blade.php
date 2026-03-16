<x-kal::form.checkbox id="terms" required>
    <span class="font-light">{{ __('k::text.input.terms_part_1') }}</span>
    <x-kal::link href="#" :value="__('k::text.input.terms_part_2')" />
    <x-slot:error>
        <x-kal::form.error for="terms" />
    </x-slot:error>
</x-kal::form.checkbox>
