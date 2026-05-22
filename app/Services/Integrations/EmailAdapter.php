<?php

namespace App\Services\Integrations;

use App\Mail\TaskAssignedMail;
use App\Mail\TaskCompletedMail;
use Illuminate\Support\Facades\Mail;

class EmailAdapter
{
    /**
     * Send task assignment email (queued)
     */
    public function notifyAsync($task, $assignedUser): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        Mail::queue(new TaskAssignedMail($task, $assignedUser));
    }

    /**
     * Send task completion email
     */
    public function notifyCompletion($task, $completedByUser): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        Mail::queue(new TaskCompletedMail($task, $completedByUser));
    }

    /**
     * Check if email is configured
     */
    public function isConfigured(): bool
    {
        return config('mail.default') !== null;
    }
}

