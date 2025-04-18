@use(Thehouseofel\Kalion\Infrastructure\Services\Kalion)
@php($field = Kalion::getLoginFieldData())

<x-kal::layout.guest title="Login" cardTitle="Sign in to your account">

    <form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-6">
        @csrf
        <div>
            <label for="{{ $field->name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $field->label }}</label>
            <input
                    type="{{ $field->type }}"
                    name="{{ $field->name }}"
                    id="{{ $field->name }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="{{ $field->placeholder }}"
                    value="{{ old($field->name) }}"
                    required
            />
            @error($field->name)
                <div class="p-2 mt-1 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-900 dark:text-red-400" role="alert">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div>
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
            <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800">
                </div>
                <div class="ml-3 text-sm">
                    <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
                </div>
            </div>
            <a href="{{ route('password.reset') }}" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">Forgot password?</a>
        </div>
        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Sign in</button>
        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
            Don’t have an account yet? <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline dark:text-blue-500">Sign up</a>
        </p>
    </form>

</x-kal::layout.guest>