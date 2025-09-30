<?php

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;

describe('LoginRequest', function () {
    it('authorizes all requests', function () {
        $request = new LoginRequest();
        expect($request->authorize())->toBeTrue();
    });

    it('has correct validation rules', function () {
        $request = new LoginRequest();
        $rules = $request->rules();

        expect($rules)->toHaveKey('email');
        expect($rules)->toHaveKey('password');
        expect($rules['email'])->toContain('required');
        expect($rules['email'])->toContain('email');
        expect($rules['password'])->toContain('required');
        expect($rules['password'])->toContain('string');
    });

    it('passes validation with valid data', function () {
        $data = [
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $request = new LoginRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->passes())->toBeTrue();
    });

    it('fails validation when email is missing', function () {
        $data = [
            'password' => 'password123'
        ];

        $request = new LoginRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    it('fails validation when password is missing', function () {
        $data = [
            'email' => 'john@example.com'
        ];

        $request = new LoginRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('password'))->toBeTrue();
    });

    it('fails validation when email is invalid', function () {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123'
        ];

        $request = new LoginRequest();
        $validator = Validator::make($data, $request->rules());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('email'))->toBeTrue();
    });

    it('has custom validation messages', function () {
        $request = new LoginRequest();
        $messages = $request->messages();

        expect($messages)->toHaveKey('email.required');
        expect($messages)->toHaveKey('email.email');
        expect($messages)->toHaveKey('password.required');
        expect($messages['email.required'])->toBe('El correo es obligatorio.');
        expect($messages['email.email'])->toBe('Debes ingresar un correo válido.');
        expect($messages['password.required'])->toBe('La contraseña es obligatoria.');
    });
});