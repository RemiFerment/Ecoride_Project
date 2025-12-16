<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'EcopiecePerDayStat')]
class EcopiecePerDayStat
{
    #[ODM\Id]
    public ?string $id = null;

    #[ODM\Field(type: 'date')]
    public \DateTime $date;

    #[ODM\Field(type: 'int')]
    public int $ecopieces = 0;
}
