<?php
// src/Service/PanierService.php
namespace App\Service;

use App\Entity\ElementPanier;
use App\Entity\Panier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class PanierService
{
    private $requestStack;
    private $entityManager;
    private $security;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getPanier(): Panier
    {
        $session = $this->requestStack->getSession();
        $sessionId = $session->getId();
        
        // Vérifier si l'utilisateur est connecté
        $user = $this->security->getUser();
        
        $panierRepository = $this->entityManager->getRepository(Panier::class);
        
        // Trouver le panier existant
        $panier = null;
        
        if ($user instanceof User) {
            // Chercher le panier de l'utilisateur connecté
            $panier = $panierRepository->findOneBy(['user' => $user]);
            
            if (!$panier) {
                // Si aucun panier utilisateur, vérifier s'il y a un panier de session
                $panierSession = $panierRepository->findOneBy(['sessionId' => $sessionId]);
                
                if ($panierSession) {
                    // Transformer le panier de session en panier utilisateur
                    $panierSession->setUser($user);
                    $panierSession->setSessionId(null);
                    $this->entityManager->flush();
                    return $panierSession;
                }
                
                // Créer un nouveau panier pour l'utilisateur
                $panier = new Panier();
                $panier->setUser($user);
                $panier->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($panier);
                $this->entityManager->flush();
            }
        } else {
            // Chercher le panier anonyme par sessionId
            $panier = $panierRepository->findOneBy(['sessionId' => $sessionId]);
            
            if (!$panier) {
                // Créer un nouveau panier anonyme
                $panier = new Panier();
                $panier->setSessionId($sessionId);
                $panier->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($panier);
                $this->entityManager->flush();
            }
        }
    
        return $panier;
    }

    public function ajouterProduit(string $nomProduit, float $prix, string $image, int $quantite = 1, ?string $produitId = null): void
    {
        $panier = $this->getPanier();
        
        // Vérifier si le produit existe déjà dans le panier
        $elementExistant = null;
        foreach ($panier->getElements() as $element) {
            if ($element->getNomProduit() === $nomProduit) {
                $elementExistant = $element;
                break;
            }
        }
        
        if ($elementExistant) {
            // Mettre à jour la quantité
            $elementExistant->setQuantite($elementExistant->getQuantite() + $quantite);
        } else {
            // Créer un nouvel élément
            $element = new ElementPanier();
            $element->setNomProduit($nomProduit);
            $element->setPrix($prix);
            $element->setImage($image);
            $element->setQuantite($quantite);
            $element->setProduitId($produitId);
            $element->setPanier($panier);
            
            $panier->addElement($element);
        }
        
        $this->entityManager->persist($panier);
        $this->entityManager->flush();
    }

    public function supprimerProduit(int $elementId): void
    {
        $elementRepository = $this->entityManager->getRepository(ElementPanier::class);
        $element = $elementRepository->find($elementId);
        
        if ($element) {
            $panier = $element->getPanier();
            $panier->removeElement($element);
            
            $this->entityManager->remove($element);
            $this->entityManager->flush();
        }
    }

    public function modifierQuantite(int $elementId, int $quantite): void
    {
        if ($quantite <= 0) {
            $this->supprimerProduit($elementId);
            return;
        }
        
        $elementRepository = $this->entityManager->getRepository(ElementPanier::class);
        $element = $elementRepository->find($elementId);
        
        if ($element) {
            $element->setQuantite($quantite);
            $this->entityManager->flush();
        }
    }

    public function viderPanier(): void
    {
        $panier = $this->getPanier();
        
        foreach ($panier->getElements()->toArray() as $element) {
            $panier->removeElement($element);
            $this->entityManager->remove($element);
        }
        
        $this->entityManager->flush();
    }
    
    public function getNombreProduits(): int
    {
        return $this->getPanier()->getNombreArticles();
    }
    
    public function getTotal(): float
    {
        return $this->getPanier()->getTotal();
    }
}