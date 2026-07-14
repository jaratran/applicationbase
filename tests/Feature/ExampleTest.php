<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_from_root_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_is_redirected_from_root_to_panel(): void
    {
        $user = new User();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('panel.index'));
    }
}
