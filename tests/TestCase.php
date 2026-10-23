<?php

namespace Tests;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::clearRuntimeCache();
    }

    protected function tearDown(): void
    {
        SiteSetting::clearRuntimeCache();
        parent::tearDown();
    }
}
