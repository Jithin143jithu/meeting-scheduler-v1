<?php

namespace App\Repositories;

use App\Models\EmailTemplate;

class EmailTemplateRepository
{
    public function findById(int $id): ?EmailTemplate
    {
        return EmailTemplate::find($id);
    }

    public function findBySlug(string $slug): ?EmailTemplate
    {
        return EmailTemplate::where('slug', $slug)->first();
    }

    public function findByType(string $type): ?EmailTemplate
    {
        return EmailTemplate::where('template_type', $type)
            ->where('is_active', true)
            ->first();
    }

    public function findAllByType(string $type): array
    {
        return EmailTemplate::where('template_type', $type)
            ->where('is_active', true)
            ->get()
            ->toArray();
    }

    public function create(array $data): EmailTemplate
    {
        return EmailTemplate::create($data);
    }

    public function update(int $id, array $data): int
    {
        return EmailTemplate::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) EmailTemplate::destroy($id);
    }
}
