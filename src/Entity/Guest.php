<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuestRepository")
 */
class Guest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $childUnder7;

    /**
     * @ORM\Column(type="boolean")
     */
    private $presence;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $placed;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Table", inversedBy="guests")
     */
    private $weddingTable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getChildUnder7(): ?bool
    {
        return $this->childUnder7;
    }

    public function setChildUnder7(?bool $childUnder7): self
    {
        $this->childUnder7 = $childUnder7;

        return $this;
    }

    public function getPresence(): ?bool
    {
        return $this->presence;
    }

    public function setPresence(bool $presence): self
    {
        $this->presence = $presence;

        return $this;
    }

    public function getPlaced(): ?bool
    {
        return $this->placed;
    }

    public function setPlaced(bool $placed): self
    {
        $this->placed = $placed;

        return $this;
    }

    public function getWeddingTable(): ?Table
    {
        return $this->weddingTable;
    }

    public function setWeddingTable(?Table $weddingTable): self
    {
        $this->weddingTable = $weddingTable;

        return $this;
    }
}
