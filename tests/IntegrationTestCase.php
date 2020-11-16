<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntegrationTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Schema::create('lit_users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->boolean('two_fa_enabled')->default(false);
            $table->string('two_fa_secret')->nullable();
        });
    }

    public function tearDown(): void
    {
        Schema::drop('lit_users');
        parent::tearDown();
    }
}
