<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TouchTemplateBlock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'html_content',
        'text_content',
        'default_values',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_values' => 'json',
    ];

    protected $appends = [
        'formatted_html',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($block) {
            if (empty($block->slug)) {
                $block->slug = Str::slug($block->name);
            }
        });
    }

    public function getFormattedHtmlAttribute()
    {
        if (empty($this->html_content)) {
            return '';
        }

        $html = $this->html_content;
        
        // Remove extra whitespace between tags
        $html = preg_replace('/>\s+</', ">\n<", $html);
        
        // Add newlines after closing tags
        $html = preg_replace('/<\/([^>]*)>/', "</$1>\n", $html);
        
        // Add newlines before opening tags (except inline elements)
        $html = preg_replace('/(?<!^)(<(?!\/|a|span|strong|em|i|b|u|small)[^>]*>)/', "\n$1", $html);
        
        // Indent based on nesting level
        $lines = explode("\n", $html);
        $level = 0;
        $formatted = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Decrease level for closing tags
            if (preg_match('/<\/[^>]*>/', $line)) {
                $level = max(0, $level - 1);
            }
            
            // Add indentation
            $formatted[] = str_repeat('    ', $level) . $line;
            
            // Increase level for opening tags (not self-closing)
            if (preg_match('/<[^\/][^>]*[^\/]>/', $line)) {
                $level++;
            }
        }
        
        return implode("\n", $formatted);
    }

    public function parseContent(array $variables = [], int $maxDepth = 5): array
    {
        $html = $this->html_content;
        $text = $this->text_content ?? strip_tags($this->html_content);

        // First parse any nested blocks
        if ($maxDepth > 0) {
            $html = $this->parseNestedBlocks($html, $variables, $maxDepth);
            $text = $this->parseNestedBlocks($text, $variables, $maxDepth);
        }

        // Then parse variables
        return [
            'html' => $this->parseVariables($html, $variables),
            'text' => $this->parseVariables($text, $variables),
        ];
    }

    protected function parseNestedBlocks(string $content, array $variables, int $maxDepth): string
    {
        return preg_replace_callback(
            '/\{\{\s*block\.([\w-]+)\s*\}\}/',
            function ($matches) use ($variables, $maxDepth) {
                $blockSlug = $matches[1];
                $nestedBlock = self::where('slug', $blockSlug)->where('is_active', true)->first();

                if (!$nestedBlock) {
                    return $matches[0]; // Return original if block not found
                }

                // Parse the nested block with decremented depth
                $parsed = $nestedBlock->parseContent($variables, $maxDepth - 1);
                return $parsed['html']; // Use HTML content for both HTML and text templates
            },
            $content
        );
    }

    protected function parseVariables(string $content, array $variables): string
    {
        return preg_replace_callback(
            '/\{\{\s*block\.([\w]+)\s*\}\}/',
            function ($matches) use ($variables) {
                $key = $matches[1];
                return $variables[$key] ?? $matches[0];
            },
            $content
        );
    }

    public function getAvailableVariables(): Collection
    {
        preg_match_all('/\{\{\s*block\.([\w]+)\s*\}\}/', $this->html_content, $matches);
        return collect($matches[1] ?? [])->unique()->sort()->values();
    }

    public function getPreviewHtml(array $variables = []): string
    {
        $parsed = $this->parseContent($variables);
        return $parsed['html'];
    }

    public function getPreviewText(array $variables = []): string
    {
        $parsed = $this->parseContent($variables);
        return $parsed['text'];
    }
}
