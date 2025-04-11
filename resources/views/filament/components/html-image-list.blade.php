@php
$images = collect(preg_match_all('/<img[^>]+src=([\'"])(.*?)\1/i', $getRecord()->html_content, $matches) ? $matches[2] : []);
@endphp

@if($images->isNotEmpty())
<div class="space-y-2">
    <h3 class="text-lg font-medium">Images in Content</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($images as $src)
            <div class="relative group bg-gray-100 rounded-lg p-4">
                <div class="text-sm text-gray-600 break-all">
                    <div class="font-medium mb-1">Image URL:</div>
                    <code class="text-xs">{{ $src }}</code>
                </div>
                <div class="mt-2">
                    <div class="font-medium text-sm mb-1">Preview:</div>
                    <div class="relative aspect-video bg-white rounded border overflow-hidden">
                        <img 
                            src="{{ $src }}" 
                            alt="Content image" 
                            class="object-contain w-full h-full p-2"
                            onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-sm text-red-500\'><svg class=\'w-5 h-5 mr-2\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'></path></svg>Image not accessible</div>'"
                        >
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
