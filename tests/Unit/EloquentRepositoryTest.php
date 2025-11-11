<?php

declare(strict_types=1);

use App\DTO\Auth\RegisterDTO;
use App\DTO\User\UserDTO;
use App\Models\User;
use App\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('returns all models with findAll', function (): void {
    User::factory()->count(3)->create();

    $repo = new class(new User()) extends EloquentRepository {};

    $all = $repo->findAll();

    expect($all)->toBeInstanceOf(Illuminate\Support\Collection::class);
    expect($all->count())->toBe(3);
});

it('paginates models with paginate', function (): void {
    User::factory()->count(20)->create();

    $repo = new class(new User()) extends EloquentRepository {};

    $page = $repo->paginate(10);

    expect($page)->toBeInstanceOf(Illuminate\Pagination\Paginator::class);
    expect($page->count())->toBe(10);
});

it('finds model by id or returns null', function (): void {
    $user = User::factory()->create();

    $repo = new class(new User()) extends EloquentRepository {};

    $found = $repo->findById($user->id);

    expect($found)->toBeInstanceOf(User::class);
    expect($repo->findById(999999))->toBeNull();
});

it('creates a model using Data DTO', function (): void {
    $repo = new class(new User()) extends EloquentRepository {};

    $dto = new RegisterDTO(name: 'Repo User', email: 'repo@example.com', password: 'secret');

    $created = $repo->create($dto);

    expect($created)->toBeInstanceOf(User::class);
    expect(User::where('email', 'repo@example.com')->exists())->toBeTrue();
});

it('updates a model or throws when not found', function (): void {
    $user = User::factory()->create(['name' => 'Before']);

    $repo = new class(new User()) extends EloquentRepository {};

    $dto = new UserDTO(name: 'After', email: $user->email);

    $updated = $repo->update($user->id, $dto);

    expect($updated)->toBeInstanceOf(User::class);
    expect($updated->name)->toBe('After');

    // non-existent should throw
    $this->expectException(ModelNotFoundException::class);
    $repo->update(999999, $dto);
});

it('deletes a model or throws when not found', function (): void {
    $user = User::factory()->create();

    $repo = new class(new User()) extends EloquentRepository {};

    $result = $repo->delete($user->id);

    expect($result)->toBeTrue();
    expect(User::where('id', $user->id)->exists())->toBeFalse();

    $this->expectException(ModelNotFoundException::class);
    $repo->delete(999999);
});
