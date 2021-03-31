<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrackingRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=TrackingRepository::class)
 * @ORM\Table(indexes={@ORM\Index(name="id_state_idx", columns={"id", "status"})})
 */
class Tracking
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delivery_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $registered_at;

    /**
     * @ORM\Column(type="uuid", length=255)
     */
    private $tracking_number;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDeliveryAt(): ?\DateTimeInterface
    {
        return $this->delivery_at;
    }

    public function setDeliveryAt(?\DateTimeInterface $delivery_at): self
    {
        $this->delivery_at = $delivery_at;

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registered_at;
    }

    public function setRegisteredAt(?\DateTimeInterface $registered_at): self
    {
        $this->registered_at = $registered_at;

        return $this;
    }

    public function getTrackingNumber(): UuidV4
    {
        return $this->tracking_number;
    }

    public function setTrackingNumber(UuidV4 $tracking_number): self
    {
        $this->tracking_number = $tracking_number;

        return $this;
    }
}
