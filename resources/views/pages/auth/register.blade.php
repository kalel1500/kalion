<x-kal::layout.guest title="Register" cardTitle="Create an account">

    <x-kal::form method="POST" action="{{ route('register') }}">
        <div>
            <x-kal::input.label for="name" value="Your name"/>
            <x-kal::input type="text" id="name" :value="old('name')" required/>
            <x-kal::input.error for="name" />
        </div>
        <div>
            <x-kal::input.label for="email" value="Your email"/>
            <x-kal::input type="email" id="email" :value="old('email')" required/>
            <x-kal::input.error for="email" />
        </div>
        <div>
            <x-kal::input.label for="password" value="Password"/>
            <x-kal::input type="password" id="password" required/>
            <x-kal::input.error for="password" />
        </div>
        <div>
            <x-kal::input.label for="password_confirmation" value="Confirm password"/>
            <x-kal::input type="password" id="password_confirmation" required/>
            <x-kal::input.error for="password_confirmation" />
        </div>

        <x-kal::input.full.checkbox id="terms" required>
            <span class="font-light">I accept the</span> <x-kal::link value="Terms and Conditions"/>
            <x-slot:error>
                <x-kal::input.error for="terms" />
            </x-slot:error>
        </x-kal::input.full.checkbox>

        <x-kal::form.button>Create an account</x-kal::form.button>

        <x-kal::form.question-link value="Already have an account?" link="Login here" href="{{ route('login') }}"/>
    </x-kal::form>

</x-kal::layout.guest>