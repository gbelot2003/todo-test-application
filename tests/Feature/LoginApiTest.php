<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\OauthClients;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        \Artisan::call('passport:install', ['-vvv' => true]);
    }

    /** @test */
    public function login_test()
    {

        factory(User::class, 1)->create(['id' => 1]);

        $oauth_client = OauthClients::findOrFail(2);
        $secret = $oauth_client->secret;
        $user = User::findOrFail(1);

        $body = [
            'grant_type' => 'password',
            'client_id' => '2',
            'client_secret' => $secret,
            'username' => $user->email,
            'password' => 'password',
            'scope' => '*',
        ];

        $this->assertDatabaseHas('oauth_clients', ['secret' => $secret]);
        $this->assertDatabaseHas('users', ['email' => $user->email]);

        $this->json('POST', 'oauth/token', $body, ['Content-Type' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure(['token_type', 'expires_in', 'access_token', 'refresh_token']);
    }

    /** @test */
    public function register_test()
    {
        $body = [
            'name' => 'Gerardo',
            'email' => 'gbelot@tester.com',
            'password' => 'password01'
        ];

        $this->json('POST', 'api/v1/register', $body, ['Accept' => 'application/json'])
            ->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'gbelot@tester.com']);
    }
}
