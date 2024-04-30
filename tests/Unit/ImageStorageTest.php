<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class ImageStorageTest extends TestCase
{
    /** @test */
    public function it_saves_uploaded_image()
    {
        Storage::fake('public');


        // Path to the existing image file
        $imagePath = public_path('admin-assets/img/avatar.png');

        // Create an UploadedFile instance from the existing image file
        $image = new UploadedFile($imagePath, 'avatar.png');
        $ext = $image->getClientOriginalExtension();

        $image = ImageManager::gd()->read($image);
        $image->resize(300, 275);

        $newName = date('Y-m-d_H-i-s') . '.' . $ext;
        // Encode the image using an encoder object
        $encoder = new AutoEncoder(); // This will automatically detect the output format
        $encodedImage = $image->encode($encoder);

        // If you want to convert the encoded image to a string
        $imageData = (string) $encodedImage;

        // Store the image in the fake filesystem
        Storage::disk('public')->put('uploads/users/' . $newName, $imageData);


        // Assert that the image exists in the correct location
        $this->assertTrue(Storage::disk('public')->exists('uploads/users/' . $newName));
    }
}
