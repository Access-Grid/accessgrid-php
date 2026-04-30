<?php

namespace AccessGrid\Models;

class PublishTemplateResult
{
    public ?string $id;
    public ?string $status;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->status = $data['status'] ?? null;
    }
}
