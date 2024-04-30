<?php

namespace Tests\Unit;

use App\Http\Controllers\AuthController;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_validates_all_fields()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => 1234567890,
            'password' => 'password',
            'password_confirmation'=>'password',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'), // Example of an uploaded image file
        ];
        $validator = Validator::make($data, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|numeric|unique:users',
            'password' => 'required|confirmed',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_creates_user()
    {

        // Mock request data
        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => 1234567890,
            'password' => 'password',
            'password_confirmation' => 'password',
            'image' =>  UploadedFile::fake()->image('avatar.jpg')

        ];
        echo($requestData['image']);

        // Create a request instance
        $request = new Request($requestData);

        // Create a new instance of AuthController
        $controller = new AuthController();

        // Call the controller method to create a user
        $response = $controller->processRegister($request);
        echo($response->getData()->status);
        // Assert that the user is created successfully
        $this->assertTrue($response->getData()->status);
        $this->assertNotNull($response->getData()->user);
    }

    // /** @test */
    // public function it_generates_authentication_token()
    // {
    //     // Mock request data
    //     $requestData = [
    //         'name' => 'John Doe',
    //         'email' => 'john@example.com',
    //         'phone_number' => '1234567890',
    //         'password' => 'password',
    //         'password_confirmation' => 'password',
    //         // Add image data if required
    //     ];

    //     // Create a request instance
    //     $request = new Request($requestData);

    //     // Create a new instance of AuthController
    //     $controller = new AuthController();

    //     // Call the controller method to create a user
    //     $response = $controller->processRegister($request);

    //     // Assert that the authentication token is generated successfully (customize the assertions based on your application logic)
    //     $this->assertEquals(true, $response->json('status'));
    //     $this->assertNotNull($response->json('token'));
    // }


}
