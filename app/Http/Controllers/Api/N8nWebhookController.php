<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\N8nService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class N8nWebhookController extends Controller
{
    public function __construct(
        private readonly N8nService $n8nService
    ) {}

    /**
     * Handle incoming webhook from n8n
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'event_type' => 'required|string',
                'workflow_id' => 'required|string',
                'payload' => 'required|array'
            ]);

            // Process the webhook through N8nService
            $result = $this->n8nService->handleInboundWebhook($validated);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to process webhook'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error processing n8n webhook', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
