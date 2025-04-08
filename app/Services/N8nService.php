<?php

namespace App\Services;

use App\Models\WorkflowEvent;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nService
{
    private string $baseUrl;
    private string $apiKey;
    private string $webhookPath;

    public function __construct()
    {
        $this->baseUrl = config('services.n8n.base_url');
        $this->apiKey = config('services.n8n.api_key');
        $this->webhookPath = config('services.n8n.webhook_path');
    }

    /**
     * Push an event to n8n webhook
     */
    public function pushEvent(WorkflowEvent $event): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->getWebhookUrl(), [
                'event_type' => $event->event_type,
                'workflow_type' => $event->workflow_type,
                'payload' => $event->payload,
                'timestamp' => now()->toIso8601String(),
                'event_id' => $event->id,
            ]);

            $this->handleResponse($event, $response);
            return $response->successful();
        } catch (\Exception $e) {
            $this->handleError($event, $e);
            return false;
        }
    }

    /**
     * Handle webhook requests from n8n
     */
    public function handleInboundWebhook(array $payload): array
    {
        try {
            // Validate webhook signature if provided
            if (!$this->validateWebhookSignature()) {
                Log::warning('Invalid n8n webhook signature');
                return ['success' => false, 'message' => 'Invalid signature'];
            }

            // Process the webhook payload
            // You can implement specific logic based on the payload here
            Log::info('Received n8n webhook', ['payload' => $payload]);

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Error processing n8n webhook', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get the full webhook URL
     */
    private function getWebhookUrl(): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($this->webhookPath, '/');
    }

    /**
     * Handle API response
     */
    private function handleResponse(WorkflowEvent $event, Response $response): void
    {
        if ($response->successful()) {
            $event->markAsProcessed();
            Log::info('Successfully pushed event to n8n', [
                'event_id' => $event->id,
                'response' => $response->json()
            ]);
        } else {
            $errorMessage = $response->json()['message'] ?? $response->body();
            $event->markAsFailed($errorMessage);
            Log::error('Failed to push event to n8n', [
                'event_id' => $event->id,
                'status_code' => $response->status(),
                'error' => $errorMessage
            ]);
        }
    }

    /**
     * Handle errors during API communication
     */
    private function handleError(WorkflowEvent $event, \Exception $e): void
    {
        $event->markAsFailed($e->getMessage());
        Log::error('Error pushing event to n8n', [
            'event_id' => $event->id,
            'error' => $e->getMessage()
        ]);
    }

    /**
     * Validate webhook signature from n8n
     * You can implement your own signature validation logic here
     */
    private function validateWebhookSignature(): bool
    {
        // Implement your webhook signature validation logic
        // This is a placeholder that always returns true
        return true;
    }
}
