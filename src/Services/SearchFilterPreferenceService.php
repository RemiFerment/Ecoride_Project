<?php

namespace App\Services;

use App\Document\UserPreferences;
use Doctrine\ODM\MongoDB\DocumentManager;

final class SearchFilterPreferenceService
{
    public function __construct(private DocumentManager $dm) {}

    /**
     * @param bool $filterSmoking  Whether the user wants a non-smoking driver
     * @param bool $filterAnimals  Whether the user wants no animals
     * @param array $carpoolings   List of Carpooling entities to filter
     */
    public function PreferenceFilter(bool $filterSmoking, bool $filterAnimals, array $carpoolings): array
    {
        $filtered = [];

        foreach ($carpoolings as $carpooling) {
            $driverPreferences = $this->dm
                ->getRepository(UserPreferences::class)
                ->findOneBy(['userId' => (string) $carpooling->getCreatedBy()->getId()]);

            if (!$driverPreferences) {
                $filtered[] = $carpooling;
                continue;
            }

            $driverNoSmoking = $driverPreferences->isSmokingAllowed();
            $driverNoAnimals = $driverPreferences->isAnimalsAllowed();

            if (
                (!$filterSmoking || $driverNoSmoking) &&
                (!$filterAnimals || $driverNoAnimals)
            ) {
                $filtered[] = $carpooling;
            }
        }

        return $filtered;
    }
}
