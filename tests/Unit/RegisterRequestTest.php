<?php

use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('RegisterRequest', function () {
    it('authorizes all requests', function () {
        $request = new RegisterRequest();
        expect($request->authorize())->toBeTrue();
    });

    it('has correct validation rules', function () {
        $request = new RegisterRequest();
        $rules = $request->rules();

        expect($rules)->toHaveKey('name');
        expect($rules)->toHaveKey('email');
        expect($rules)->toHaveKey('password');
        expect($rules['name'])->toContain('required');
        expect($rules['email'])->toContain('unique:users');
        expect($rules['password'])->toContain('confirmed');
    });

    it('passes validation with valid data', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    it('fails validation when name is missing', function () {
        $data = [
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('name'))->toBeTrue();
    });

    it('fails validation when email is missing', function () {
        $data = [
            'name' => 'John Doe',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    it('fails validation when password is missing', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    it('fails validation when email is invalid', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    it('fails validation when email already exists', function () {
        User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    it('fails validation when password is too short', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    it('fails validation when password confirmation does not match', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    it('has custom validation messages', function () {
        $request = new RegisterRequest();
        $messages = $request->messages();

        expect($messages)->toHaveKey('name.required');
        expect($messages)->toHaveKey('email.required');
        expect($messages)->toHaveKey('email.unique');
        expect($messages)->toHaveKey('password.confirmed');
        expect($messages['name.required'])->toBe('El nombre es obligatorio.');
        expect($messages['email.unique'])->toBe('Este correo ya está registrado.');
    });
});