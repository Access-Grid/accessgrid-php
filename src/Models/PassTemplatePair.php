<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class PassTemplatePair
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $name;
    public ?string $createdAt;
    public ?TemplateInfo $androidTemplate;
    public ?TemplateInfo $iosTemplate;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->androidTemplate = isset($data['android_template'])
            ? new TemplateInfo($client, $data['android_template'])
            : null;
        $this->iosTemplate = isset($data['ios_template'])
            ? new TemplateInfo($client, $data['ios_template'])
            : null;
    }
}
