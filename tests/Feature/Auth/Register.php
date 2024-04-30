<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class Register extends TestCase
{
    /** @test */
    public function it_registers_a_user_with_valid_data()
    {
        // Prepare request data
        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $validator = Validator::make($requestData, [
            'name' => 'required|min:3',
            'email'=> 'required|email|unique:users',
            'phone_number' => 'required|unique:users',
            'password' => 'required|confirmed',
            'image' => 'required|mimes:png,jpg,jpeg',
        ]);

        if ($validator->passes()){


        // Mock the User creation
        $user = User::factory()->make();
        $user->id = 1; // Mock the ID
        User::shouldReceive('create')
            ->once()
            ->andReturn($user);

        // Mock the ImageManager and Storage
        Storage::fake('public');
        $imageManager = $this->createMock(ImageManager::class);
        $imageManager->expects($this->once())
            ->method('gd')
            ->willReturn($imageManager);
        $imageManager->expects($this->once())
            ->method('resize')
            ->willReturn($imageManager);



        // Create a new request instance and bind the request data to it
        $request = new Request($requestData);

        // Call the method to be tested
        $response = (new AuthController())->processRegister($request);
        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'user' => $user->toArray(),
            ]);

        }
    }

}
