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

    /** @test */
    public function login_test()
    {
        // Migrar las bases de datos
        //\Artisan::call('migrate',['-vvv' => true]);
        \Artisan::call('passport:install', ['-vvv' => true]);

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
    
    }
}
