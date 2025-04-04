<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}

        @if($previewSubject)
            <div class="space-y-4 mt-4">
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-2">Subject Preview</h3>
                    <div class="p-2 bg-gray-50 rounded">
                        {{ $previewSubject }}
                    </div>
                </div>

                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-2">HTML Preview</h3>
                    <div class="border rounded p-4">
                        {!! $previewHtml !!}
                    </div>
                </div>

                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-2">Plain Text Preview</h3>
                    <div class="p-2 bg-gray-50 rounded font-mono whitespace-pre-wrap">
                        {{ $previewText }}
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
