@use(Thehouseofel\Kalion\Infrastructure\Services\Kalion)
@php($field = Kalion::getLoginFieldData())

<x-kal::layout.guest title="Login" cardTitle="Sign in to your account">

    <x-kal::form method="POST" action="{{ route('login') }}">
        <div>
            <x-kal::input.label :for="$field->name" :value="$field->label"/>
            <x-kal::input :type="$field->type" :id="$field->name" class="text-base" :value="old($field->name)" required/>
            <x-kal::input.error :for="$field->name" />
        </div>
        <div>
            <x-kal::input.label for="password" value="Password"/>
            <x-kal::input type="password" id="password" class="text-base" required/>
        </div>
        <div class="flex items-center justify-between">
            <x-kal::input.full.checkbox id="remember" labelText="Remember me" />
            <x-kal::link href="{{ route('password.reset') }}" class="text-sm" value="Forgot password?"/>
        </div>

        <x-kal::form.button>Sign in</x-kal::form.button>

        <x-kal::form.question-link value="Don’t have an account yet?" link="Sign up" href="{{ route('register') }}"/>
    </x-kal::form>

</x-kal::layout.guest>