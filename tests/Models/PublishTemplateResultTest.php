<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\PublishTemplateResult;

class PublishTemplateResultTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $result = new PublishTemplateResult([
            'id' => 'tmpl_123',
            'status' => 'in-review',
        ]);

        $this->assertEquals('tmpl_123', $result->id);
        $this->assertEquals('in-review', $result->status);
    }

    public function testConstructionWithReadyStatus(): void
    {
        $result = new PublishTemplateResult([
            'id' => 'tmpl_456',
            'status' => 'ready',
        ]);

        $this->assertEquals('ready', $result->status);
    }

    public function testConstructionWithEmptyData(): void
    {
        $result = new PublishTemplateResult([]);

        $this->assertNull($result->id);
        $this->assertNull($result->status);
    }
}
