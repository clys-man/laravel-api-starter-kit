<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('UserController', function (): void {
    beforeEach(function (): void {
        $this->nonExistentId = 'non-existent-id';
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    });

    it('returns a collection of users by default', function (): void {
        User::factory()->count(5)->create();

        $response = $this->getJson(route('users:index'));

        $response->assertOk();
        expect($response->json())
            ->toHaveKeys(['data']);
        expect($response->json('data'))->toHaveCount(6);
    });

    it('returns paginated users when requested', function (): void {
        User::factory()->count(15)->create();

        $response = $this->getJson(route('users:index', ['paginated' => true]));

        $response->assertOk();
        expect($response->json())
            ->toHaveKeys(['data', 'links', 'meta']);
        expect($response->json('data'))->toHaveCount(15);
    });

    it('respects pagination parameters', function (): void {
        User::factory()->count(30)->create();

        $response = $this->getJson(route('users:index', [
            'paginated' => true,
            'page' => 2,
            'per_page' => 10,
        ]));

        $response->assertOk();
        expect($response->json('meta.current_page'))->toBe(2);
        expect($response->json('meta.per_page'))->toBe(10);
        expect($response->json('data'))->toHaveCount(10);
    });

    it('shows a specific user with correct structure', function (): void {
        $response = $this->getJson(route('users:show', $this->user->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'name',
            'email' => [
                'address',
                'verified',
            ],
            'created' => [
                'human',
                'string',
            ],
        ]);

        $response->assertJsonPath('id', $this->user->id);
        $response->assertJsonPath('name', $this->user->name);
        $response->assertJsonPath('email.address', $this->user->email);
    });

    it('updates a user and returns correct structure', function (): void {
        $newData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson(route('users:update', $this->user->id), $newData);

        $response->assertOk();
        $response->assertJsonStructure([
            'id',
            'name',
            'email' => [
                'address',
                'verified',
            ],
            'created' => [
                'human',
                'string',
            ],
        ]);

        $response->assertJsonPath('name', 'Updated Name');
        $response->assertJsonPath('email.address', 'updated@example.com');
    });

    it('returns validation errors when updating with invalid data', function (): void {
        $invalidData = [
            'name' => '',
            'email' => 'not-an-email',
        ];

        $response = $this->putJson(route('users:update', $this->user->id), $invalidData);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email']);
    });

    it('returns 404 when updating non-existent user', function (): void {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson(
            route('users:update', $this->nonExistentId),
            $updateData
        );

        $response->assertNotFound();
        $response->assertJson([
            'title' => 'Not Found',
            'status' => 404,
            'detail' => 'An unexpected error occurred.',
        ]);
    });

    it('returns 404 when deleting non-existent user', function (): void {
        $response = $this->deleteJson(
            route('users:destroy', $this->nonExistentId)
        );

        $response->assertNotFound();
        $response->assertJson([
            'title' => 'Not Found',
            'status' => 404,
            'detail' => 'An unexpected error occurred.',
        ]);
    });

    it('does not alter database when updating non-existent user', function (): void {
        $originalUsers = User::all()->toArray();

        $this->putJson(
            route('users:update', $this->nonExistentId),
            ['name' => 'Updated']
        );

        expect(User::all()->toArray())->toEqual($originalUsers);
    });

    it('does not alter database when deleting non-existent user', function (): void {
        $originalUsers = User::all()->toArray();

        $this->deleteJson(route('users:destroy', $this->nonExistentId));

        expect(User::all()->toArray())->toEqual($originalUsers);
    });

    it('deletes a user and returns no content', function (): void {
        $response = $this->deleteJson(route('users:destroy', $this->user->id));

        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    });

    it('returns 404 when trying to show non-existent user', function (): void {
        $nonExistentId = 9999;

        $response = $this->getJson(route('users:show', $nonExistentId));

        $response->assertNotFound();
    });

    it('returns 401 when unauthenticated', function (): void {
        auth()->forgetGuards();

        $this->getJson(route('users:index'))->assertUnauthorized();
        $this->getJson(route('users:show', $this->user->id))->assertUnauthorized();
        $this->putJson(route('users:update', $this->user->id), [])->assertUnauthorized();
        $this->deleteJson(route('users:destroy', $this->user->id))->assertUnauthorized();
    });

    it('has correct email verification status in response', function (): void {
        $verifiedUser = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->getJson(route('users:show', $verifiedUser->id));

        $response->assertJsonPath('email.verified', true);

        $unverifiedUser = User::factory()->create(['email_verified_at' => null]);

        $response = $this->getJson(route('users:show', $unverifiedUser->id));

        $response->assertJsonPath('email.verified', false);
    });
});
