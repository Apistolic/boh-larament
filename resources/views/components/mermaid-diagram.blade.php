@props(['content'])

<div class="relative">
    <div class="mermaid">
        {{ $content }}
    </div>
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
