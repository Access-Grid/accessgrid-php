<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\PassTemplatePair;
use AccessGrid\Models\TemplateInfo;

class PassTemplatePairTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $data = [
            'id' => 'pair_123',
            'name' => 'Employee Badge Pair',
            'created_at' => '2025-01-01T00:00:00Z',
            'android_template' => [
                'id' => 'tmpl_android_456',
                'name' => 'Employee Badge Android',
                'platform' => 'android',
            ],
            'ios_template' => [
                'id' => 'tmpl_ios_789',
                'name' => 'Employee Badge iOS',
                'platform' => 'apple',
            ],
        ];

        $pair = new PassTemplatePair($this->client, $data);

        $this->assertEquals('pair_123', $pair->id);
        $this->assertEquals('Employee Badge Pair', $pair->name);
        $this->assertEquals('2025-01-01T00:00:00Z', $pair->createdAt);

        $this->assertInstanceOf(TemplateInfo::class, $pair->androidTemplate);
        $this->assertEquals('tmpl_android_456', $pair->androidTemplate->id);
        $this->assertEquals('Employee Badge Android', $pair->androidTemplate->name);
        $this->assertEquals('android', $pair->androidTemplate->platform);

        $this->assertInstanceOf(TemplateInfo::class, $pair->iosTemplate);
        $this->assertEquals('tmpl_ios_789', $pair->iosTemplate->id);
        $this->assertEquals('Employee Badge iOS', $pair->iosTemplate->name);
        $this->assertEquals('apple', $pair->iosTemplate->platform);
    }

    public function testConstructionWithIosOnly(): void
    {
        $data = [
            'id' => 'pair_ios_only',
            'name' => 'iOS Only Pair',
            'ios_template' => [
                'id' => 'tmpl_ios',
                'name' => 'iOS Template',
                'platform' => 'apple',
            ],
        ];

        $pair = new PassTemplatePair($this->client, $data);

        $this->assertInstanceOf(TemplateInfo::class, $pair->iosTemplate);
        $this->assertEquals('tmpl_ios', $pair->iosTemplate->id);
        $this->assertNull($pair->androidTemplate);
    }

    public function testConstructionWithAndroidOnly(): void
    {
        $data = [
            'id' => 'pair_android_only',
            'name' => 'Android Only Pair',
            'android_template' => [
                'id' => 'tmpl_android',
                'name' => 'Android Template',
                'platform' => 'android',
            ],
        ];

        $pair = new PassTemplatePair($this->client, $data);

        $this->assertInstanceOf(TemplateInfo::class, $pair->androidTemplate);
        $this->assertEquals('tmpl_android', $pair->androidTemplate->id);
        $this->assertNull($pair->iosTemplate);
    }

    public function testConstructionWithMinimalData(): void
    {
        $pair = new PassTemplatePair($this->client, ['id' => 'pair_minimal']);

        $this->assertEquals('pair_minimal', $pair->id);
        $this->assertNull($pair->name);
        $this->assertNull($pair->createdAt);
        $this->assertNull($pair->androidTemplate);
        $this->assertNull($pair->iosTemplate);
    }

    public function testConstructionWithEmptyData(): void
    {
        $pair = new PassTemplatePair($this->client, []);

        $this->assertNull($pair->id);
        $this->assertNull($pair->name);
        $this->assertNull($pair->createdAt);
        $this->assertNull($pair->androidTemplate);
        $this->assertNull($pair->iosTemplate);
    }
}

class TemplateInfoTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $info = new TemplateInfo($this->client, [
            'id' => 'tmpl_info_123',
            'name' => 'Template Name',
            'platform' => 'apple',
        ]);

        $this->assertEquals('tmpl_info_123', $info->id);
        $this->assertEquals('Template Name', $info->name);
        $this->assertEquals('apple', $info->platform);
    }

    public function testConstructionWithMinimalData(): void
    {
        $info = new TemplateInfo($this->client, ['id' => 'tmpl_minimal']);

        $this->assertEquals('tmpl_minimal', $info->id);
        $this->assertNull($info->name);
        $this->assertNull($info->platform);
    }

    public function testConstructionWithEmptyData(): void
    {
        $info = new TemplateInfo($this->client, []);

        $this->assertNull($info->id);
        $this->assertNull($info->name);
        $this->assertNull($info->platform);
    }
}
