<?php

namespace App\Services;

use App\Document\GlobalStat;
use Doctrine\ODM\MongoDB\DocumentManager;

final class GlobalStatService
{
    public const CARPOOL_STAT = "allCarpool";
    public const ACCOUNT_STAT = "allAccount";
    public const ECOPIECE_STAT = "allEcopiece";

    function __construct(private DocumentManager $dm) {}

    /**
     * Increase MongoDB collection GlobalStats Document
     */
    public function incGlobalStat(string $param, int $amount = 1): static
    {
        if (!in_array($param, [
            self::CARPOOL_STAT,
            self::ACCOUNT_STAT,
            self::ECOPIECE_STAT,
        ], true)) {
            throw new \InvalidArgumentException("Invalid stat field: $param");
        }

        $this->dm->createQueryBuilder(GlobalStat::class)
            ->updateOne()
            ->field($param)->inc($amount)
            ->getQuery()
            ->execute();
        return $this;
    }
    /**
     * Show the statistic wished
     */
    public function showGlobalStat(string $param): ?int
    {
        if (!in_array($param, [
            self::CARPOOL_STAT,
            self::ACCOUNT_STAT,
            self::ECOPIECE_STAT,
        ], true)) {
            throw new \InvalidArgumentException("Invalid stat field: $param");
        }

        $query = $this->dm->createQueryBuilder(GlobalStat::class)
            ->find()
            ->select($param)
            ->hydrate(false)
            ->getQuery();

        return $query->getSingleResult()[$param] ?? null;
    }
}
