<?php

namespace App\Services;

use App\Services\Integrations\SlackAdapter;
use App\Services\Integrations\GoogleCalendarAdapter;
use App\Services\Integrations\EmailAdapter;

class IntegrationService
{
    protected $slackAdapter;
    protected $googleCalendarAdapter;
    protected $emailAdapter;

    public function __construct()
    {
        $this->slackAdapter = new SlackAdapter();
        $this->googleCalendarAdapter = new GoogleCalendarAdapter();
        $this->emailAdapter = new EmailAdapter();
    }

    /**
     * Send task assignment notification across all channels
     */
    public function notifyTaskAssignment($task, $assignedUser): void
    {
        // Dispatch queued jobs for each integration (non-blocking)
        $this->slackAdapter->notifyAsync($task, $assignedUser);
        $this->emailAdapter->notifyAsync($task, $assignedUser);
    }

    /**
     * Send task completion notification
     */
    public function notifyTaskCompletion($task, $completedByUser): void
    {
        $this->slackAdapter->notifyCompletion($task, $completedByUser);
        $this->emailAdapter->notifyCompletion($task, $completedByUser);
    }

    /**
     * Sync due dates to Google Calendar
     */
    public function syncToCalendar($task): void
    {
        $this->googleCalendarAdapter->syncAsync($task);
    }

    /**
     * Get integration status
     */
    public function getStatus(): array
    {
        return [
            'slack' => $this->slackAdapter->isConfigured(),
            'google_calendar' => $this->googleCalendarAdapter->isConfigured(),
            'email' => $this->emailAdapter->isConfigured(),
        ];
    }
}
