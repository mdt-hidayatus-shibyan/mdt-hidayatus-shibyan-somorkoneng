<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Ustadz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UstadzRepositoryInterface
{
    /**
     * Get paginated Ustadz list with optional filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    /**
     * Get available Users for creating Ustadz profile.
     */
    public function getAvailableUsersForCreate(): Collection;

    /**
     * Get available Users for editing Ustadz profile.
     */
    public function getAvailableUsersForEdit(?int $currentUserId = null): Collection;

    /**
     * Find Ustadz by ID or fail.
     */
    public function findById(int $id): ?Ustadz;

    /**
     * Create new Ustadz record.
     */
    public function create(array $data): Ustadz;

    /**
     * Update existing Ustadz record.
     */
    public function update(Ustadz $ustadz, array $data): bool;

    /**
     * Delete Ustadz record.
     */
    public function delete(Ustadz $ustadz): bool;

    /**
     * Create new User account and assign 'ustadz' role.
     */
    public function createUserAccount(array $userData): User;

    /**
     * Update User account data.
     */
    public function updateUserAccount(User $user, array $userData): bool;

    /**
     * Find User by username.
     */
    public function findUserByUsername(string $username): ?User;

    /**
     * Find Ustadz by NIGM or NIK.
     */
    public function findByNigmOrNik(?string $nigm, ?string $nik): ?Ustadz;
}
