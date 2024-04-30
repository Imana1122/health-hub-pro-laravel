<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HashPasswordTest extends TestCase
{
    /**
     * Test hashing a password.
     */
    public function test_password_hashing(): void
    {
        // Plain text password
        $password = 'secret123';

        // Hash the password
        $hashedPassword = Hash::make($password);

        // Check if the password was hashed successfully
        $this->assertNotEmpty($hashedPassword);

        // Verify that the hashed password matches the plain text password
        $this->assertTrue(Hash::check($password, $hashedPassword));
    }
}
