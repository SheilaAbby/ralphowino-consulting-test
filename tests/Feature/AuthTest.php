<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    
    public function user_can_login_with_valid_credentials(){ //only a user with a valid email and password can log in
    	$user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $response->assertStatus(302);

    }

    public function user_cannot_login_with_invalid_credentials(){//user with invalid credentials can not log in.Error messages are displayed

        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'invalid'
        ]);

        $response->assertSessionHasErrors();


    }

    public  function user_can_register_with_valid_credentials(){//requires valid email,name,password and confirmed password to register
    	$user = factory(User::class)->make();

        $response = $this->post('register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(302);

    }
    public function user_cannot_register_with_existing_credentials(){//a user can not register using details already existing in the database

    	$user = factory(User::class)->make();

        $response = $this->post('register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'invalid'
        ]);

        $response->assertSessionHasErrors();

    }
    public function user_can_request_for_reset_password_code(){ //will disaplay a form from where a user can enter their email/token to reset 

    	 $response = $this->get('/password/reset/token');

        $response->assertStatus(200);

    }
    public function user_can_reset_password_with_valid_code(){ //allows user to reset a new password.

        $user = factory(User::class)->create();

        $token = Password::createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));

    }






}
