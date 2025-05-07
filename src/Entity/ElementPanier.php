<?php

namespace App\Entity;

use App\Repository\ElementPanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementPanierRepository::class)]
class ElementPanier {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomProduit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $produitId = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?Panier $panier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getProduitId(): ?string
    {
        return $this->produitId;
    }

    public function setProduitId(?string $produitId): static
    {
        $this->produitId = $produitId;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;

        return $this;
    }
    
    /**
     * Calcule le sous-total pour cet élément du panier
     * 
     * @return float Le produit du prix par la quantité
     */
    public function getSousTotal(): float
    {
        return $this->prix * $this->quantite;
    }
}