<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\DateType;

class CarpoolPerDayStat
{
    #[ODM\Id]
    public ?string $id = null;

    #[ODM\Field()]
    public ?DateType $date = null;

    #[ODM\Field()]
    public ?int $carpoolsLaunch = null;
}
