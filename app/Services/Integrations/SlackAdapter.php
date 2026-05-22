<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Log;

class SlackAdapter
{
    protected $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = config('services.slack.webhook_url');
    }

    /**
     * Send task assignment notification (queued)
     */
    public function notifyAsync($task, $assignedUser): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        // Queue the notification to avoid blocking
        dispatch(function () use ($task, $assignedUser) {
            $this->sendSlackMessage(
                "🎯 Task Assigned: {$task->title}",
                "Assigned to {$assignedUser->name}",
                "#36a64f"
            );
        })->onQueue('notifications');
    }

    /**
     * Send task completion notification
     */
    public function notifyCompletion($task, $completedByUser): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        dispatch(function () use ($task, $completedByUser) {
            $this->sendSlackMessage(
                "✅ Task Completed: {$task->title}",
                "Completed by {$completedByUser->name}",
                "#2ecc71"
            );
        })->onQueue('notifications');
    }

    /**
     * Send message to Slack webhook
     */
    protected function sendSlackMessage(string $title, string $text, string $color): void
    {
        try {
            \Http::post($this->webhookUrl, [
                'attachments' => [
                    [
                        'title' => $title,
                        'text' => $text,
                        'color' => $color,
                        'ts' => time(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Slack notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if Slack is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->webhookUrl);
    }
}

