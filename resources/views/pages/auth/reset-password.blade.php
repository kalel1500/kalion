<x-kal::layout.guest :title="__('k::auth.reset_password.title')" :cardTitle="__('k::auth.reset_password.card_title')">

    <x-kal::form method="POST" action="{{ route('password.update') }}" class="mt-4 lg:mt-5">
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <x-kal::form.input type="email" :label="__('k::text.input.email')" id="email" :value="old('email', $request->email)" required autofocus autocomplete="email" />

        <!-- Password -->
        <x-kal::form.input type="password" :label="__('k::text.input.password')" id="password" required autocomplete="new-password" />

        <!-- Confirm Password -->
        <x-kal::form.input type="password" :label="__('k::text.input.password_confirmation')" id="password_confirmation" required autocomplete="new-password" />

        <x-kal::partials.form-btn>{{ __('k::auth.reset_password.btn') }}</x-kal::partials.form-btn>
    </x-kal::form>

</x-kal::layout.guest>

