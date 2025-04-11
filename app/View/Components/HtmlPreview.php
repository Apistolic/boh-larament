<?php

namespace App\View\Components;

use Illuminate\View\Component;

class HtmlPreview extends Component
{
    public function __construct(
        public ?string $content = null,
        public array $variables = [],
    ) {
        if (is_callable($content)) {
            $this->content = $content();
        }
    }

    public function render()
    {
        return view('components.html-preview', [
            'content' => $this->content,
            'variables' => $this->variables,
        ]);
    }
}
