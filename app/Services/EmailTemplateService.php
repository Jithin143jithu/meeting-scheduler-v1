<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\DB;

class EmailTemplateService
{
    protected EmailTemplateRepository $templateRepository;
    protected ActivityLogRepository $activityLogRepository;

    public function __construct(
        EmailTemplateRepository $templateRepository,
        ActivityLogRepository $activityLogRepository
    ) {
        $this->templateRepository = $templateRepository;
        $this->activityLogRepository = $activityLogRepository;
    }

    public function createTemplate(int $userId, array $data): EmailTemplate
    {
        return DB::transaction(function () use ($userId, $data) {
            $template = $this->templateRepository->create($data);

            if ($userId) {
                $this->activityLogRepository->create([
                    'user_id' => $userId,
                    'action' => 'email_template_created',
                    'model_type' => 'EmailTemplate',
                    'model_id' => $template->id,
                ]);
            }

            return $template;
        });
    }

    public function updateTemplate(int $userId, int $templateId, array $data): EmailTemplate
    {
        return DB::transaction(function () use ($userId, $templateId, $data) {
            $this->templateRepository->update($templateId, $data);

            $this->activityLogRepository->create([
                'user_id' => $userId,
                'action' => 'email_template_updated',
                'model_type' => 'EmailTemplate',
                'model_id' => $templateId,
            ]);

            return $this->templateRepository->findById($templateId);
        });
    }

    public function getTemplate(int $templateId): ?EmailTemplate
    {
        return $this->templateRepository->findById($templateId);
    }

    public function getTemplateBySlug(string $slug): ?EmailTemplate
    {
        return $this->templateRepository->findBySlug($slug);
    }

    public function getTemplatesByType(string $type): array
    {
        return $this->templateRepository->findByType($type);
    }
}
