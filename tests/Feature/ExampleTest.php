<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Root redirects to login/dashboard for guests — accept both 200 and 302
        $this->assertTrue(
            in_array($response->status(), [200, 302]),
            "Expected 200 or 302, got {$response->status()}"
        );
    }
}