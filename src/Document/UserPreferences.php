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
    private bool $smokingAllowed = false;

    #[ODM\Field(type: "bool")]
    private bool $animalsAllowed = false;

    #[ODM\Field(type: "string")]
    private ?string $customPreferences = '';


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

    public function isSmokingAllowed(): bool
    {
        return $this->smokingAllowed;
    }

    public function setSmokingAllowed(bool $smokingAllowed): static
    {
        $this->smokingAllowed = $smokingAllowed;
        return $this;
    }

    public function isAnimalsAllowed(): bool
    {
        return $this->animalsAllowed;
    }

    public function setAnimalsAllowed(bool $animalsAllowed): static
    {
        $this->animalsAllowed = $animalsAllowed;
        return $this;
    }

    public function getCustomPreferences(): ?string
    {
        if ($this->customPreferences === null) {
            return '';
        }
        return $this->customPreferences;
    }

    public function setCustomPreferences(?string $customPreferences): static
    {
        $this->customPreferences = $customPreferences ?? '';
        return $this;
    }
}
