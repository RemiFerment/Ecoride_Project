<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use App\Document\UserPreferences;

class UserPreferencesRepository extends DocumentRepository
{
    public function findByUserId(string $userId): ?UserPreferences
    {
        return $this->findOneBy(['userId' => $userId]);
    }
}
