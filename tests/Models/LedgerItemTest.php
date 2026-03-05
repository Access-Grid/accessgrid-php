<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\LedgerItem;
use AccessGrid\Models\LedgerItemAccessPass;
use AccessGrid\Models\LedgerItemPassTemplate;

class LedgerItemTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $data = [
            'created_at' => '2025-06-15T10:30:00Z',
            'amount' => 1.50,
            'id' => 'li_123',
            'ex_id' => 'li_123',
            'kind' => 'access_pass_issued',
            'metadata' => ['access_pass_ex_id' => 'pass_456'],
            'access_pass' => [
                'id' => 'pass_456',
                'ex_id' => 'pass_456',
                'full_name' => 'Jane Doe',
                'state' => 'active',
                'metadata' => ['department' => 'engineering'],
                'unified_access_pass_ex_id' => 'uap_789',
                'pass_template' => [
                    'id' => 'tmpl_abc',
                    'ex_id' => 'tmpl_abc',
                    'name' => 'Employee Badge',
                    'protocol' => 'desfire',
                    'platform' => 'apple',
                    'use_case' => 'employee_badge',
                ],
            ],
        ];

        $item = new LedgerItem($this->client, $data);

        $this->assertEquals('li_123', $item->id);
        $this->assertEquals('2025-06-15T10:30:00Z', $item->createdAt);
        $this->assertEquals(1.50, $item->amount);
        $this->assertEquals('access_pass_issued', $item->kind);
        $this->assertEquals('pass_456', $item->metadata['access_pass_ex_id']);

        $this->assertInstanceOf(LedgerItemAccessPass::class, $item->accessPass);
        $this->assertEquals('pass_456', $item->accessPass->id);
        $this->assertEquals('Jane Doe', $item->accessPass->fullName);
        $this->assertEquals('active', $item->accessPass->state);
        $this->assertEquals('engineering', $item->accessPass->metadata['department']);
        $this->assertEquals('uap_789', $item->accessPass->unifiedAccessPassExId);

        $this->assertInstanceOf(LedgerItemPassTemplate::class, $item->accessPass->passTemplate);
        $this->assertEquals('tmpl_abc', $item->accessPass->passTemplate->id);
        $this->assertEquals('Employee Badge', $item->accessPass->passTemplate->name);
        $this->assertEquals('desfire', $item->accessPass->passTemplate->protocol);
        $this->assertEquals('apple', $item->accessPass->passTemplate->platform);
        $this->assertEquals('employee_badge', $item->accessPass->passTemplate->useCase);
    }

    public function testConstructionWithNullAccessPass(): void
    {
        $data = [
            'created_at' => '2025-06-15T10:30:00Z',
            'amount' => 0.75,
            'id' => 'li_no_pass',
            'ex_id' => 'li_no_pass',
            'kind' => 'adjustment',
            'metadata' => [],
            'access_pass' => null,
        ];

        $item = new LedgerItem($this->client, $data);

        $this->assertEquals('li_no_pass', $item->id);
        $this->assertEquals(0.75, $item->amount);
        $this->assertEquals('adjustment', $item->kind);
        $this->assertNull($item->accessPass);
    }

    public function testConstructionWithAccessPassButNoPassTemplate(): void
    {
        $data = [
            'created_at' => '2025-06-15T10:30:00Z',
            'amount' => 1.00,
            'id' => 'li_no_tmpl',
            'ex_id' => 'li_no_tmpl',
            'kind' => 'access_pass_issued',
            'metadata' => [],
            'access_pass' => [
                'id' => 'pass_456',
                'ex_id' => 'pass_456',
                'full_name' => 'John Smith',
                'state' => 'active',
                'metadata' => [],
            ],
        ];

        $item = new LedgerItem($this->client, $data);

        $this->assertInstanceOf(LedgerItemAccessPass::class, $item->accessPass);
        $this->assertEquals('pass_456', $item->accessPass->id);
        $this->assertEquals('John Smith', $item->accessPass->fullName);
        $this->assertNull($item->accessPass->passTemplate);
        $this->assertNull($item->accessPass->unifiedAccessPassExId);
    }

    public function testConstructionWithMinimalData(): void
    {
        $item = new LedgerItem($this->client, [
            'id' => 'li_minimal',
            'ex_id' => 'li_minimal',
        ]);

        $this->assertEquals('li_minimal', $item->id);
        $this->assertNull($item->createdAt);
        $this->assertNull($item->amount);
        $this->assertNull($item->kind);
        $this->assertNull($item->metadata);
        $this->assertNull($item->accessPass);
    }

    public function testConstructionWithEmptyData(): void
    {
        $item = new LedgerItem($this->client, []);

        $this->assertNull($item->id);
        $this->assertNull($item->createdAt);
        $this->assertNull($item->amount);
        $this->assertNull($item->kind);
        $this->assertNull($item->metadata);
        $this->assertNull($item->accessPass);
    }

    public function testIdReadsFromExId(): void
    {
        $item = new LedgerItem($this->client, [
            'ex_id' => 'li_from_ex_id',
        ]);

        $this->assertEquals('li_from_ex_id', $item->id);
    }

    public function testAccessPassIdReadsFromExId(): void
    {
        $item = new LedgerItem($this->client, [
            'access_pass' => [
                'ex_id' => 'pass_from_ex_id',
                'full_name' => 'Test User',
                'state' => 'active',
            ],
        ]);

        $this->assertEquals('pass_from_ex_id', $item->accessPass->id);
    }

    public function testPassTemplateIdReadsFromExId(): void
    {
        $item = new LedgerItem($this->client, [
            'access_pass' => [
                'ex_id' => 'pass_123',
                'pass_template' => [
                    'ex_id' => 'tmpl_from_ex_id',
                    'name' => 'Badge',
                ],
            ],
        ]);

        $this->assertEquals('tmpl_from_ex_id', $item->accessPass->passTemplate->id);
    }
}
