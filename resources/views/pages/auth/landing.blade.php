@use(Thehouseofel\Kalion\Infrastructure\Services\Kalion)
@php($field = Kalion::getLoginFieldData())

<x-kal::layout.landing>
    <form method="POST" action="{{ route('login') }}" class="bg-gray-900 opacity-75 w-full shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4" >
        @csrf
        <div class="mb-4">
            <label class="block text-blue-300 py-2 font-bold mb-2" for="{{ $field->name }}">
                {{ $field->label }}
            </label>
            <input
                class="shadow-sm appearance-none border rounded-sm w-full p-3 text-gray-700 leading-tight focus:ring-3 transform transition hover:scale-105 duration-300 ease-in-out"
                id="{{ $field->name }}"
                name="{{ $field->name }}"
                type="{{ $field->type }}"
                placeholder="{{ $field->placeholder }}"
                value="{{ old($field->name) }}"
            />
        </div>
        @error($field->name)
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                {{ $message }}
            </div>
        @enderror

        <div class="flex items-center justify-between pt-4">
            <button
                class="bg-linear-to-r from-purple-800 to-green-500 hover:from-pink-500 hover:to-green-500 text-white font-bold py-2 px-4 rounded-sm focus:ring-3 transform transition hover:scale-105 duration-300 ease-in-out"
                type="submit"
            >
                Sign In
            </button>
        </div>
    </form>
</x-kal::layout.landing>