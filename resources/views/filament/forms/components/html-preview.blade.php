@php
    $record = $this->getRecord();
    $content = $record ? $record->content : '';
    
    // If this is an email, wrap it in the template/layout
    if ($record && $record->type === \App\Models\Touch::TYPE_EMAIL) {
        $template = $record->template;
        $layout = $template?->layout;
        
        if ($layout) {
            $content = str_replace(
                ['{content}', '{subject}'],
                [$content, $record->subject ?? ''],
                $layout->html_content
            );
        }
    }
@endphp

<div class="prose max-w-none dark:prose-invert">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-medium">Content Preview</h3>
            <div class="flex space-x-2">
                <button
                    type="button"
                    class="text-sm px-3 py-1 rounded-md bg-primary-500 text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    x-data
                    x-on:click="$clipboard($refs.htmlContent.innerHTML)"
                >
                    Copy HTML
                </button>
                <button
                    type="button"
                    class="text-sm px-3 py-1 rounded-md bg-primary-500 text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    x-data
                    x-on:click="$clipboard($refs.htmlContent.innerText)"
                >
                    Copy Text
                </button>
            </div>
        </div>
        
        <div class="border rounded-lg overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2 text-sm font-medium">
                Preview
            </div>
            <div 
                x-ref="htmlContent"
                class="bg-white dark:bg-gray-800 p-4 prose max-w-none dark:prose-invert"
            >
                {!! $content !!}
            </div>
        </div>

        @if($record && $record->type === \App\Models\Touch::TYPE_EMAIL && (!$record->template || !$record->template->layout))
            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200 rounded-lg">
                <p class="text-sm">
                    ⚠️ This email content is not wrapped in a template/layout. The recipient will see the raw content without any styling or branding.
                </p>
            </div>
        @endif
    </div>
</div>
