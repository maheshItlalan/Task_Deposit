<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthApiControllerTest extends TestCase
{
    use RefreshDatabase; // Reset the database before each test
    public function testApiLoginWithValidCredentials() : void
    {
        $user = User::factory()->create([
            'email' => 'abc@gmail.com',
            'password' => bcrypt('1234'),
        ]);

        // Send a POST request API endpoint with valid credentials
        $response = $this->postJson('/Apilogin', [
            'email' => 'abc@gmail.com',
            'password' => '1234',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function testApiLoginWithInvalidCredentials() : void
    {
        $response = $this->postJson('/Apilogin', [
            'email' => 'test@gmail.com',
            'password' => '123$ABC',
        ]);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);
    }
}
