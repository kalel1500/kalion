<x-kal::layout.guest title="Forgot password" cardTitle="Forgot your password?">

    <x-slot:cardText>
        Don't fret! Just type in your email and we will send you a code to reset your password!
    </x-slot:cardText>

    <x-kal::form method="POST" action="#" class="mt-4 lg:mt-5">
        <div>
            <x-kal::input.label for="email" value="Your email"/>
            <x-kal::input type="email" id="email" placeholder="name@company.com" :value="old('email')" required/>
            <x-kal::input.error for="email" />
        </div>
        <x-kal::input.full.checkbox id="terms" required>
            <span class="font-light">I accept the</span> <x-kal::link href="#" value="Terms and Conditions"/>
            <x-slot:error>
                <x-kal::input.error for="terms" />
            </x-slot:error>
        </x-kal::input.full.checkbox>
        <x-kal::form.button>Reset password</x-kal::form.button>
    </x-kal::form>

</x-kal::layout.guest>