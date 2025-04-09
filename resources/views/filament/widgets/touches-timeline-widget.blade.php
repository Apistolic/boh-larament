<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-medium tracking-tight mb-4">
            Recent & Upcoming Touches
        </h2>

        <div class="space-y-4">
            @foreach($this->getTouches() as $touch)
                <div class="flex items-start space-x-4 p-2 rounded-lg {{ $touch['is_past'] ? 'bg-gray-50' : 'bg-blue-50' }}">
                    <div class="flex-shrink-0">
                        @switch($touch['type'])
                            @case('email')
                                <x-heroicon-o-envelope class="w-6 h-6 text-gray-500"/>
                                @break
                            @case('sms')
                                <x-heroicon-o-chat-bubble-left class="w-6 h-6 text-gray-500"/>
                                @break
                            @case('call')
                                <x-heroicon-o-phone class="w-6 h-6 text-gray-500"/>
                                @break
                            @default
                                <x-heroicon-o-document class="w-6 h-6 text-gray-500"/>
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $touch['contact'] }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $touch['subject'] ?: ucfirst($touch['type']) }}
                        </p>
                        <div class="flex items-center mt-1">
                            <span class="text-xs text-gray-500">
                                {{ $touch['is_past'] ? 'Executed' : 'Scheduled' }}: {{ $touch['date']->diffForHumans() }}
                            </span>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $touch['status'] === 'sent' ? 'bg-green-100 text-green-800' : ($touch['status'] === 'failed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($touch['status']) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($this->getTouches()->isEmpty())
                <div class="text-center text-gray-500 py-4">
                    No touches found in the past 7 days or scheduled for the next 7 days.
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
