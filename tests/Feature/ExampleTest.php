<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_home_landing_page_is_displayed(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('JPJFit - Fitness Monitoring System');
    }
}
