<x-kal::layout.app package title="Compare html">
    <x-kal::section>
        <h1 class="mb-5 text-3xl font-bold dark:text-white">Comparar HTML</h1>
        <div class="flex items-center justify-center gap-10">
            <x-kal::form.textarea id="textarea-a" label="Html A" class="w-2/5" inputClass="whitespace-nowrap scroller text-gray-500!" spellcheck="false"/>
            <x-kal::form.textarea id="textarea-b" label="Html B" class="w-2/5" inputClass="whitespace-nowrap scroller text-gray-500!" spellcheck="false"/>
            <div>
                <x-kal::button id="compareHtml">Comparar</x-kal::button>
            </div>
        </div>
        <div class="flex justify-center">
            <x-kal::card class="mt-5 p-0 max-w-lg! min-w-96">
                <div id="result-ok" class="hidden p-3 rounded-sm border border-green-500 bg-green-50 text-green-900 focus:border-green-500 focus:ring-green-500 dark:border-green-500 dark:bg-gray-900 dark:text-green-400"> Los dos bloques html tienen las mismas clases</div>
                <div id="result-nok" class="hidden p-3 rounded-sm border border-red-500   bg-red-50   text-red-900 focus:border-red-500 focus:ring-red-500 dark:border-red-500 dark:bg-gray-900 dark:text-red-500">Hay diferencias entre las clases de los dos bloques html</div>
            </x-kal::card>
        </div>
    </x-kal::section>
</x-kal::layout.app>
