<x-kal::layout.app package title="Compare html" xmlns:x-kal="http://www.w3.org/1999/html">
    <x-kal::section>
        <h1 class="mb-5 text-3xl font-bold dark:text-white">{{ __('Compare') }} HTML</h1>
        <div class="flex items-center justify-center gap-10">

            <div class="w-2/5">
                <x-kal::input.label for="textarea-a" value="Html A"/>
                <x-kal::input.textarea id="textarea-a" class="whitespace-nowrap scroller text-gray-500!" spellcheck="false" rows="8" placeholder="Text..."/>
            </div>

            <div class="w-2/5">
                <x-kal::input.label for="textarea-b" value="Html B"/>
                <x-kal::input.textarea id="textarea-b" class="whitespace-nowrap scroller text-gray-500!" spellcheck="false" rows="8" placeholder="Text..."/>
            </div>

            <div>
                <x-kal::button id="compareHtml">{{ __('Compare') }}</x-kal::button>
            </div>
        </div>
        <div class="flex justify-center">
            <x-kal::card class="mt-5 p-0 max-w-lg! min-w-96">
                <div id="result-ok" class="hidden p-3 rounded-sm border border-green-500 bg-green-50 text-green-900 focus:border-green-500 focus:ring-green-500 dark:border-green-500 dark:bg-gray-900 dark:text-green-400">{{ __('k::text.compare_html_ok') }}</div>
                <div id="result-nok" class="hidden p-3 rounded-sm border border-red-500   bg-red-50   text-red-900 focus:border-red-500 focus:ring-red-500 dark:border-red-500 dark:bg-gray-900 dark:text-red-500">{{ __('k::text.compare_html_nok') }}</div>
            </x-kal::card>
        </div>
    </x-kal::section>
</x-kal::layout.app>
