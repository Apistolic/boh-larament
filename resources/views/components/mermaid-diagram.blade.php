@props(['content'])

<div 
    wire:ignore
    x-data="{ 
        init() {
            this.refresh();
            this.$watch('$wire.data.sequence_diagram', () => this.refresh());
        },
        refresh() {
            this.$refs.diagram.innerHTML = this.$wire.data.sequence_diagram || '';
            if (window.mermaid) {
                window.mermaid.init(undefined, this.$refs.diagram);
            }
        }
    }"
    class="relative space-y-2"
>
    <div x-ref="diagram" class="mermaid"></div>
    <button type="button" 
        x-on:click="refresh()"
        class="text-sm px-3 py-1 bg-primary-600 text-white rounded-lg hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
    >
        Refresh Diagram
    </button>
</div>

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/mermaid@10.6.1/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({ 
        startOnLoad: true,
        theme: 'default',
        securityLevel: 'loose'
    });
</script>
@endPushOnce
