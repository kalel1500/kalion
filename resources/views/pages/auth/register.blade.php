<x-kal::layout.guest :title="__('k::text.register.title')" :cardTitle="__('k::text.register.card_title')">

    <x-kal::form method="POST" action="{{ route('register') }}">
        <div>
            <x-kal::input.label for="name" :value="__('k::text.input.name')" />
            <x-kal::input type="text" id="name" :value="old('name')" required />
            <x-kal::input.error for="name" />
        </div>
        <div>
            <x-kal::input.label for="email" :value="__('k::text.input.email')" />
            <x-kal::input type="email" id="email" :value="old('email')" required />
            <x-kal::input.error for="email" />
        </div>
        <div>
            <x-kal::input.label for="password" :value="__('k::text.input.password')" />
            <x-kal::input type="password" id="password" required />
            <x-kal::input.error for="password" />
        </div>
        <div>
            <x-kal::input.label for="password_confirmation" :value="__('k::text.input.password_confirmation')" />
            <x-kal::input type="password" id="password_confirmation" required />
            <x-kal::input.error for="password_confirmation" />
        </div>

        <x-kal::input.full.checkbox id="terms" required>
            <span class="font-light">{{ __('k::text.input.terms_part_1') }}</span>
            <x-kal::link href="#" :value="__('k::text.input.terms_part_2')" />
            <x-slot:error>
                <x-kal::input.error for="terms" />
            </x-slot:error>
        </x-kal::input.full.checkbox>

        <x-kal::form.button>{{ __('k::text.register.btn') }}</x-kal::form.button>

        <x-kal::form.question-link :value="__('k::text.register.question')" :link="__('k::text.register.question_link')" href="{{ route('login') }}" />
    </x-kal::form>

</x-kal::layout.guest>