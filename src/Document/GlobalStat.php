<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'globalStats')]
class GlobalStat
{
    #[ODM\Id]
    public ?string $id = null;

    #[ODM\Field()]
    public ?int $allCarpool = null;

    #[ODM\Field()]
    public ?int $allAccount = null;

    #[ODM\Field()]
    public ?int $allEcopiece = null;
}
