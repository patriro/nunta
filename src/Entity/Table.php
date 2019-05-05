<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TableRepository")
 * @ORM\Table(name="`table`")
 */
class Table
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $placesAvailable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Guest", mappedBy="weddingTable")
     */
    private $guests;

    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }

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

    public function getPlacesAvailable(): ?int
    {
        return $this->placesAvailable;
    }

    public function setPlacesAvailable(?int $placesAvailable): self
    {
        $this->placesAvailable = $placesAvailable;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|Guest[]
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guest $guest): self
    {
        if (!$this->guests->contains($guest)) {
            $this->guests[] = $guest;
            $guest->setWeddingTable($this);
        }

        return $this;
    }

    public function addGuests($guests): self
    {
        foreach ($guests as $guest) {
            if (!$this->guests->contains($guest)) {
                $this->guests[] = $guest;
                $guest->setWeddingTable($this);
            }
        }

        return $this;
    }

    public function removeGuest(Guest $guest): self
    {
        if ($this->guests->contains($guest)) {
            $this->guests->removeElement($guest);
            // set the owning side to null (unless already changed)
            if ($guest->getWeddingTable() === $this) {
                $guest->setWeddingTable(null);
            }
        }

        return $this;
    }
}
