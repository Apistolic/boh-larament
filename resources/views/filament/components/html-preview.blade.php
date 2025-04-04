<div class="prose dark:prose-invert max-w-none">
    <div class="bg-white p-4 rounded-lg shadow">
        @if(isset($variables) && count($variables) > 0)
            <div class="text-sm text-gray-500 mb-4">
                <div class="font-medium mb-1">Available Variables:</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($variables as $variable)
                        <code class="px-2 py-1 bg-gray-100 rounded text-gray-700">{{ "{{ block.$variable }}" }}</code>
                    @endforeach
                </div>
            </div>
            <div class="border-t mb-4"></div>
        @endif

        <div class="text-sm text-gray-500 mb-2">Preview:</div>
        <div class="border-t pt-2">
            {!! $state !!}
        </div>
    </div>
</div>
