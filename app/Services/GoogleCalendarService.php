<?php

namespace App\Services;

use App\Models\GoogleAccount;
use App\Repositories\GoogleAccountRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class GoogleCalendarService
{
    protected GoogleAccountRepository $googleAccountRepository;
    protected ActivityLogRepository $activityLogRepository;
    protected GoogleCalendarClient $googleClient;

    public function __construct(
        GoogleAccountRepository $googleAccountRepository,
        ActivityLogRepository $activityLogRepository,
        GoogleCalendarClient $googleClient
    ) {
        $this->googleAccountRepository = $googleAccountRepository;
        $this->activityLogRepository = $activityLogRepository;
        $this->googleClient = $googleClient;
    }

    public function connectAccount(int $userId, array $tokenData): GoogleAccount
    {
        return DB::transaction(function () use ($userId, $tokenData) {
            $googleAccount = $this->googleAccountRepository->findByUserId($userId);

            if ($googleAccount) {
                $this->googleAccountRepository->update($googleAccount->id, $tokenData);
                $googleAccount = $this->googleAccountRepository->findById($googleAccount->id);
            } else {
                $tokenData['user_id'] = $userId;
                $googleAccount = $this->googleAccountRepository->create($tokenData);
            }

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'google_account_connected',
                'model_type' => 'GoogleAccount',
                'model_id' => $googleAccount->id,
            ]);

            return $googleAccount;
        });
    }

    public function disconnectAccount(int $userId): bool
    {
        $googleAccount = $this->googleAccountRepository->findByUserId($userId);

        if (!$googleAccount) {
            return false;
        }

        return DB::transaction(function () use ($userId, $googleAccount) {
            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'google_account_disconnected',
                'model_type' => 'GoogleAccount',
                'model_id' => $googleAccount->id,
            ]);

            return $this->googleAccountRepository->delete($googleAccount->id);
        });
    }

    public function syncCalendarEvents(int $userId): bool
    {
        return DB::transaction(function () use ($userId) {
            $googleAccount = $this->googleAccountRepository->findByUserId($userId);

            if (!$googleAccount || !$googleAccount->sync_enabled) {
                return false;
            }

            $this->googleClient->setAccessToken($googleAccount->access_token);

            try {
                $events = $this->googleClient->getCalendarEvents($googleAccount->calendar_id);
                // TODO: Process events and create availability exceptions

                $this->googleAccountRepository->update($googleAccount->id, [
                    'last_synced_at' => now(),
                    'is_synced' => true,
                ]);

                return true;
            } catch (Exception $e) {
                $this->activityLogRepository->create([
                    'user_id' => $userId,
                    'action' => 'google_sync_failed',
                    'status' => 'failed',
                    'description' => $e->getMessage(),
                ]);

                return false;
            }
        });
    }

    public function createCalendarEvent(int $userId, array $eventData): bool
    {
        $googleAccount = $this->googleAccountRepository->findByUserId($userId);

        if (!$googleAccount) {
            throw new Exception('Google account not connected');
        }

        try {
            $this->googleClient->setAccessToken($googleAccount->access_token);
            $this->googleClient->createEvent($googleAccount->calendar_id, $eventData);

            return true;
        } catch (Exception $e) {
            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'create_calendar_event_failed',
                'status' => 'failed',
                'description' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
