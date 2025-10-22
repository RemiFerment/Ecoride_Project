<?php

namespace App\Services;

use App\Document\UserPreferences;
use App\Repository\UserPreferencesRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class MongoFilterService
{
    public function __construct(private DocumentManager $dm) {}

    // public function UserPreferencesFilter(array $preferences, array $carpoolings): array
    // {
    //     //je dois récupérer les Id des utilisateurs à filtrer
    //     //récupérer les préférences sélectionnées, l'array preferences est sous forme [true,false,true,false].
    //     //Filtrer chaque id utilisateur par rapport à leur préférence
    //     foreach ($carpoolings as $carpooling) {
    //         $carpooling->getCreatedBy()->getId();
    //     }
    // }


    public function getUserPreferences(int $userId): array
    {
        $repo = $this->dm->getRepository(UserPreferences::class);
        $preferences = $repo->findOneBy(['userId' => (string) $userId]);
        dd($preferences);

        if (!$preferences) {
            // Default values if user has no preferences
            return [true, true, true, true];
        }

        return [
            $preferences->isNoSmoking(),
            $preferences->isNoAnimals(),
            $preferences->isNoMusic(),
            $preferences->isTalkative(),
        ];
    }
}
