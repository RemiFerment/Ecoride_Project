<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use App\Repository\UserPreferencesRepository;

#[ODM\Document(collection: "user_preferences", repositoryClass: UserPreferencesRepository::class)]
class UserPreferences
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    private ?string $userId = null;

    #[ODM\Field(type: "bool")]
    private bool $noSmoking = false;

    #[ODM\Field(type: "bool")]
    private bool $noAnimals = false;

    #[ODM\Field(type: "bool")]
    private bool $noMusic = false;

    #[ODM\Field(type: "bool")]
    private bool $talkative = true;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function isNoSmoking(): bool
    {
        return $this->noSmoking;
    }

    public function setNoSmoking(bool $noSmoking): self
    {
        $this->noSmoking = $noSmoking;
        return $this;
    }

    public function isNoAnimals(): bool
    {
        return $this->noAnimals;
    }

    public function setNoAnimals(bool $noAnimals): self
    {
        $this->noAnimals = $noAnimals;
        return $this;
    }

    public function isNoMusic(): bool
    {
        return $this->noMusic;
    }

    public function setNoMusic(bool $noMusic): self
    {
        $this->noMusic = $noMusic;
        return $this;
    }

    public function isTalkative(): bool
    {
        return $this->talkative;
    }

    public function setTalkative(bool $talkative): self
    {
        $this->talkative = $talkative;
        return $this;
    }
}
