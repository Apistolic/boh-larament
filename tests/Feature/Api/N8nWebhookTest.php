<?php

namespace Tests\Feature\Api;

use App\Models\WorkflowEvent;
use App\Services\N8nService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class N8nWebhookTest extends TestCase
{
    use RefreshDatabase;

    private array $validPayload;
    private $n8nServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment variables
        Config::set('services.n8n.base_url', 'http://test.n8n.local');
        Config::set('services.n8n.api_key', 'test-api-key');
        Config::set('services.n8n.webhook_path', '/webhook/test');
        
        $this->validPayload = [
            'event_type' => 'test.event',
            'workflow_id' => 'test_workflow_123',
            'payload' => [
                'message' => 'Test message',
                'data' => ['foo' => 'bar']
            ]
        ];

        // Create a base mock for N8nService
        $this->n8nServiceMock = $this->mock(N8nService::class);
    }

    #[Test]
    public function it_accepts_valid_n8n_payload(): void
    {
        // Configure mock for this test
        $this->n8nServiceMock->shouldReceive('handleInboundWebhook')
            ->once()
            ->andReturn(['success' => true, 'message' => 'Webhook processed successfully']);

        // Act
        $response = $this->postJson('/api/webhooks/n8n', $this->validPayload);

        // Assert
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        // Arrange
        $payload = [
            'payload' => ['foo' => 'bar']
        ];

        // Configure mock for this test (shouldn't be called due to validation)
        $this->n8nServiceMock->shouldNotReceive('handleInboundWebhook');

        // Act
        $response = $this->postJson('/api/webhooks/n8n', $payload);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['event_type', 'workflow_id']);
    }

    #[Test]
    public function it_handles_n8n_service_errors_gracefully(): void
    {
        // Configure mock for this test
        $this->n8nServiceMock->shouldReceive('handleInboundWebhook')
            ->once()
            ->andReturn(['success' => false, 'message' => 'Service error']);

        // Act
        $response = $this->postJson('/api/webhooks/n8n', $this->validPayload);

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Service error'
            ]);
    }

    #[Test]
    public function it_logs_errors_when_processing_fails(): void
    {
        // Configure mock for this test
        $this->n8nServiceMock->shouldReceive('handleInboundWebhook')
            ->once()
            ->andThrow(new \Exception('Processing failed'));

        // Expect log message
        Log::shouldReceive('error')
            ->once()
            ->with('Error processing n8n webhook', \Mockery::hasKey('error'));

        // Act
        $response = $this->postJson('/api/webhooks/n8n', $this->validPayload);

        // Assert
        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Internal server error'
            ]);
    }
}
