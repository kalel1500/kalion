<x-kal::layout.guest title="Register" cardTitle="Create an account">

    <form method="POST" action="{{ route('register') }}" class="space-y-4 md:space-y-6">
        @csrf
        <div>
            <x-kal::input.label for="name" value="Your name"/>
            <x-kal::input.text id="name" placeholder="Name Surname" required/>
        </div>
        <div>
            <x-kal::input.label for="email" value="Your email"/>
            <x-kal::input.email id="email" placeholder="name@company.com" required/>
        </div>
        <div>
            <x-kal::input.label for="password" value="Password"/>
            <x-kal::input.password id="password" placeholder="••••••••" required/>
        </div>
        <div>
            <x-kal::input.label for="password_confirmation" value="Confirm password"/>
            <x-kal::input.password id="password_confirmation" placeholder="••••••••" required/>
        </div>

        <x-kal::input.full.checkbox id="terms" required>
            <span class="font-light">I accept the</span> <x-kal::link value="Terms and Conditions"/>
        </x-kal::input.full.checkbox>

        <x-kal::form.button>Create an account</x-kal::form.button>

        <x-kal::form.question-link value="Already have an account?" link="Login here" href="{{ route('login') }}"/>
    </form>

</x-kal::layout.guest>