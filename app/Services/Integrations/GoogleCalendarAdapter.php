<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Log;

class GoogleCalendarAdapter
{
    protected $clientId;
    protected $clientSecret;
    protected $refreshToken;

    public function __construct()
    {
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
        $this->refreshToken = config('services.google.refresh_token');
    }

    /**
     * Sync task due dates to Google Calendar (queued)
     */
    public function syncAsync($task): void
    {
        if (!$this->isConfigured() || !$task->due_date) {
            return;
        }

        dispatch(function () use ($task) {
            $this->createCalendarEvent($task);
        })->onQueue('integrations')->delay(now()->addSeconds(5));
    }

    /**
     * Create or update calendar event
     */
    protected function createCalendarEvent($task): void
    {
        try {
            $accessToken = $this->getAccessToken();

            $eventData = [
                'summary' => $task->title,
                'description' => $task->description,
                'start' => [
                    'dateTime' => $task->due_date->toIso8601String(),
                    'timeZone' => 'UTC',
                ],
                'end' => [
                    'dateTime' => $task->due_date->addHours(1)->toIso8601String(),
                    'timeZone' => 'UTC',
                ],
            ];

            \Http::withToken($accessToken)->post(
                'https://www.googleapis.com/calendar/v3/calendars/primary/events',
                $eventData
            );
        } catch (\Exception $e) {
            Log::error('Google Calendar sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Get access token from refresh token
     */
    protected function getAccessToken(): string
    {
        $response = \Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        return $response->json('access_token');
    }

    /**
     * Check if Google Calendar is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->refreshToken);
    }
}

