<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TouchTemplateLayout extends Model
{
    use SoftDeletes;

    const TYPE_EMAIL = 'email';
    const TYPE_SMS = 'sms';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'html_content',
        'text_content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($layout) {
            if (empty($layout->slug)) {
                $layout->slug = Str::slug($layout->name);
            }
        });
    }

    public function templates()
    {
        return $this->hasMany(TouchTemplate::class, 'layout_id');
    }

    public function parseContent(array $blocks = []): array
    {
        return [
            'html' => $this->parseBlocks($this->html_content, $blocks),
            'text' => $this->parseBlocks($this->text_content ?? strip_tags($this->html_content), $blocks),
        ];
    }

    protected function parseBlocks(string $content, array $blocks): string
    {
        return preg_replace_callback(
            '/\{\{\s*block\.([\w]+)\s*\}\}/',
            function ($matches) use ($blocks) {
                $blockName = $matches[1];
                return $blocks[$blockName]['html'] ?? '';
            },
            $content
        );
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_SMS => 'SMS/Text',
        ];
    }
}
