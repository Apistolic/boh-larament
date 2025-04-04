<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TouchTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'html_content',
        'text_content',
        'target_lifecycle_stages',
        'is_active',
        'test_contact_id',
        'layout_id',
        'block_content',
    ];

    protected $casts = [
        'target_lifecycle_stages' => 'array',
        'is_active' => 'boolean',
        'block_content' => 'array',
    ];

    public function testContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'test_contact_id');
    }

    public function layout(): BelongsTo
    {
        return $this->belongsTo(TouchTemplateLayout::class, 'layout_id');
    }

    /**
     * Available merge fields for templates
     */
    public static function availableMergeFields(): array
    {
        return [
            'contact.first_name' => 'First Name',
            'contact.last_name' => 'Last Name',
            'contact.full_name' => 'Full Name',
            'contact.email' => 'Email Address',
            'contact.formatted_mobile_phone' => 'Mobile Phone',
            'contact.formatted_phone' => 'Other Phone',
            'contact.lifecycle_stage' => 'Lifecycle Stage',
        ];
    }

    /**
     * Parse merge fields in content
     */
    public function parseContent(Contact $contact, string $content): string
    {
        $fields = [
            'contact.first_name' => $contact->first_name,
            'contact.last_name' => $contact->last_name,
            'contact.full_name' => $contact->full_name,
            'contact.email' => $contact->email,
            'contact.formatted_mobile_phone' => $contact->formatted_mobile_phone,
            'contact.formatted_phone' => $contact->formatted_phone,
            'contact.lifecycle_stage' => Contact::LIFECYCLE_STAGES[$contact->lifecycle_stage] ?? $contact->lifecycle_stage,
        ];

        return preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9._]+)\s*\}\}/',
            fn($matches) => $fields[$matches[1]] ?? '',
            $content
        );
    }

    /**
     * Parse template for a specific contact
     */
    public function parseForContact(Contact $contact): array
    {
        // First parse any blocks in the template
        $blocks = [];
        if ($this->block_content) {
            foreach ($this->block_content as $blockSlug => $blockVariables) {
                $block = TouchTemplateBlock::where('slug', $blockSlug)->first();
                if ($block) {
                    $blocks[$blockSlug] = $block->parseContent($blockVariables);
                }
            }
        }

        // If we have a layout, use it
        if ($this->layout) {
            $parsed = $this->layout->parseContent($blocks);
            $html = $parsed['html'];
            $text = $parsed['text'];
        } else {
            $html = $this->html_content;
            $text = $this->text_content ?? strip_tags($html);
        }

        // Parse contact merge fields
        return [
            'subject' => $this->parseContent($contact, $this->subject),
            'html_content' => $this->parseContent($contact, $html),
            'text_content' => $this->parseContent($contact, $text),
        ];
    }
}
