<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoginAdmin()
    {
    	$admin = factory(User::class)->create(['role_id' => 1]);

    	$response = $this->json('POST', '/api/login', [
    		'email' => $admin->email,
    		'password' => 'secret'
    	]);

        $response->assertStatus(200)->assertJsonStructure(['message', 'user']);
    }

    public function testLoginFailed()
    {
    	$this->json('Post', 'api/login', [
    		'email' => 'email@email.com',
    		'password' => 'password'
    	])->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function testLogoutAdminWithSuccessResponse()
    {
    	$admin = factory(User::class)->create(['role_id' => 1]);
    	$token = $admin->createToken($admin->email)->accessToken;

    	$response = $this->json('POST', '/api/logout', [], ['Authorization' => "Bearer $token"]);	
    	$response->assertStatus(200);
    }

    public function testLogoutWithExpireToken()
    {
    	$admin = factory(User::class)->create(['role_id' => 1]);
    	$token = $admin->createToken($admin->email)->accessToken;

    	//expire token
    	$admin->AauthAccessToken()->delete();
    	$response = $this->json('POST', '/api/logout', [], ['Authorization' => "Bearer $token"]);	
    	$response->assertStatus(401);
    }
}
