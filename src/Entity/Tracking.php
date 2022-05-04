<?php

namespace App\Entity;

use App\Repository\TrackingRepository;
use Carbon\CarbonImmutable;
use Carbon\Doctrine\CarbonImmutableType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TrackingRepository::class)]
class Tracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $tracking_number;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $origin_message = null;

    #[ORM\Column(type: 'carbon_immutable')]
    private CarbonImmutable $created_at;

    #[ORM\Column(type: 'carbon_immutable')]
    private CarbonImmutable $modified_at;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): self
    {
        if (!isset($this->created_at)) {
            $this->created_at = CarbonImmutable::now();
        }

        return $this;
    }

    #[ORM\PostUpdate]
    #[ORM\PrePersist]
    public function updateModifiedAtValue(): self
    {
        $this->modified_at = CarbonImmutable::now();

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->tracking_number;
    }

    public function setTrackingNumber(string $tracking_number): self
    {
        $this->tracking_number = $tracking_number;

        return $this;
    }

    public function setOriginMessage(object $originMessage): void
    {
        $this->origin_message = $originMessage::class;
    }
}
