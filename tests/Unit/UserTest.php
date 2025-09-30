<?php

use App\Models\User;
use App\Models\Progress;

describe('User Model', function () {
    it('can be created with valid data', function () {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        expect($user->name)->toBe('John Doe');
        expect($user->email)->toBe('john@example.com');
        expect($user)->toBeInstanceOf(User::class);
    });

    it('has mass assignable attributes', function () {
        $user = new User();
        $fillable = $user->getFillable();

        expect($fillable)->toContain('name');
        expect($fillable)->toContain('email');
        expect($fillable)->toContain('password');
    });

    it('hides sensitive attributes when serialized', function () {
        $user = User::factory()->make();
        $hidden = $user->getHidden();

        expect($hidden)->toContain('password');
        expect($hidden)->toContain('remember_token');
    });

    it('casts email_verified_at to datetime', function () {
        $user = User::factory()->create([
            'email_verified_at' => '2023-01-01 12:00:00'
        ]);

        expect($user->email_verified_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('hashes password automatically', function () {
        $plainPassword = 'password123';
        $user = User::factory()->create([
            'password' => $plainPassword
        ]);

        expect($user->password)->not->toBe($plainPassword);
        expect(\Illuminate\Support\Facades\Hash::check($plainPassword, $user->password))->toBeTrue();
    });

    it('has a progress relationship', function () {
        $user = User::factory()->create();

        expect($user->progress())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('can have multiple progress records', function () {
        $user = User::factory()->create();
        $progress1 = Progress::factory()->create(['user_id' => $user->id]);
        $progress2 = Progress::factory()->create(['user_id' => $user->id]);

        expect($user->progress)->toHaveCount(2);
        expect($user->progress->first()->id)->toBe($progress1->id);
        expect($user->progress->last()->id)->toBe($progress2->id);
    });

    it('requires unique email', function () {
        User::factory()->create(['email' => 'test@example.com']);

        expect(function () {
            User::factory()->create(['email' => 'test@example.com']);
        })->toThrow(\Illuminate\Database\QueryException::class);
    });

    it('requires email to be valid format', function () {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);
    });
});