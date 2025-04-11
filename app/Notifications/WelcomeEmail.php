<?php

namespace App\Notifications;

use App\Models\TouchTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected TouchTemplate $template;

    public function __construct(TouchTemplate $template)
    {
        $this->template = $template;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Replace template variables
        $subject = $this->replaceVariables($this->template->subject, $notifiable);
        $content = $this->replaceVariables($this->template->html_content, $notifiable);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.template', [
                'content' => $content,
                'contact' => $notifiable,
            ]);
    }

    protected function replaceVariables(string $text, $notifiable): string
    {
        $replacements = [
            '{{contact.first_name}}' => $notifiable->first_name,
            '{{contact.last_name}}' => $notifiable->last_name,
            '{{contact.full_name}}' => $notifiable->first_name . ' ' . $notifiable->last_name,
            '{{contact.email}}' => $notifiable->email,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
