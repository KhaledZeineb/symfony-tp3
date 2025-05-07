<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sessionId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, ElementPanier>
     */
    #[ORM\OneToMany(targetEntity: ElementPanier::class, mappedBy: 'panier')]
    private Collection $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, ElementPanier>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(ElementPanier $element): static
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setPanier($this);
        }

        return $this;
    }

    public function removeElement(ElementPanier $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getPanier() === $this) {
                $element->setPanier(null);
            }
        }

        return $this;
    }
    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->elements as $element) {
            $total += $element->getPrix() * $element->getQuantite();
        }

        return $total;
    }
    public function getNombreArticles(): int
    {
        $nombre = 0;

        foreach ($this->elements as $element) {
            $nombre += $element->getQuantite();
        }

        return $nombre;
    }
}
