<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\EmailClick;
use App\Models\EmailOpen;
use App\Models\EmailSend;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class EmailTrackingService
{
    private Agent $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function prepareEmail(Contact $contact, string $subject, string $content): EmailSend
    {
        // Generate tracking IDs
        $emailSendId = Str::uuid();
        $pixelId = Str::random(40);
        
        // Insert tracking pixel
        $trackingPixel = '<img src="' . route('email.track.open', $pixelId) . '" width="1" height="1" alt="" />';
        
        // Process email content
        $content = $this->rewriteLinks($content, $emailSendId);
        $content .= $trackingPixel;
        
        // Create email send record
        return EmailSend::create([
            'id' => $emailSendId,
            'contact_id' => $contact->id,
            'subject' => $subject,
            'content' => $content,
            'tracking_pixel_id' => $pixelId,
            'sent_at' => now(),
        ]);
    }
    
    public function trackOpen(string $pixelId, Request $request): void
    {
        $emailSend = EmailSend::where('tracking_pixel_id', $pixelId)->first();
        
        if ($emailSend) {
            $this->agent->setUserAgent($request->userAgent());
            $location = Location::get($request->ip());
            
            EmailOpen::create([
                'email_send_id' => $emailSend->id,
                'opened_at' => now(),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'email_client' => $this->detectEmailClient($request->userAgent()),
                'device_type' => $this->agent->device(),
                'country' => $location?->countryName,
                'city' => $location?->cityName,
            ]);
        }
    }
    
    public function trackClick(string $emailSendId, string $url, Request $request): void
    {
        $this->agent->setUserAgent($request->userAgent());
        $location = Location::get($request->ip());
        
        EmailClick::create([
            'email_send_id' => $emailSendId,
            'link_url' => $url,
            'clicked_at' => now(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'device_type' => $this->agent->device(),
            'country' => $location?->countryName,
            'city' => $location?->cityName,
        ]);
    }
    
    private function rewriteLinks(string $content, string $emailSendId): string
    {
        return preg_replace_callback(
            '/<a\s+href=["\']([^"\']+)["\']/i',
            function($matches) use ($emailSendId) {
                $originalUrl = $matches[1];
                $trackingUrl = route('email.track.click', [
                    'emailSendId' => $emailSendId,
                    'url' => urlencode($originalUrl)
                ]);
                return '<a href="' . $trackingUrl . '"';
            },
            $content
        );
    }
    
    private function detectEmailClient(string $userAgent): ?string
    {
        $emailClients = [
            'Outlook' => '/Microsoft Outlook|MSOffice|Windows-Live-Mail/',
            'Apple Mail' => '/iPhone|iPad|Mac OS/',
            'Gmail' => '/Gmail/',
            'Yahoo Mail' => '/Yahoo/',
            'Thunderbird' => '/Thunderbird/',
        ];
        
        foreach ($emailClients as $client => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $client;
            }
        }
        
        return null;
    }
}
