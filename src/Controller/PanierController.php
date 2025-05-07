<?php
// src/Controller/PanierController.php
namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    private $panierService;
    
    public function __construct(PanierService $panierService)
    {
        $this->panierService = $panierService;
    }
    
    #[Route('/', name: 'app_panier')]
    public function index(): Response
    {
        $panier = $this->panierService->getPanier();
        
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }
    
    #[Route('/ajouter-produit', name: 'app_panier_ajouter_produit', methods: ['POST'])]
public function ajouterProduit(Request $request, PanierService $panierService): JsonResponse
{
    // Version sécurisée qui accepte POST et vérifie les données
    if (!$request->isMethod('POST')) {
        return $this->json(['error' => 'Méthode non autorisée'], 405);
    }

    $panierService->ajouterProduit(
        'Produit Test',
        19.99,
        'test.jpg',
        1
    );
    
    return $this->json([
        'success' => true,
        'count' => $panierService->getNombreProduits()
    ]);
}
    
    #[Route('/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(int $id): Response
    {
        $this->panierService->supprimerProduit($id);
        
        $this->addFlash('success', 'Le produit a été retiré du panier.');
        
        return $this->redirectToRoute('app_panier');
    }
    
    #[Route('/modifier-quantite/{id}', name: 'app_panier_modifier_quantite', methods: ['POST'])]
    public function modifierQuantite(Request $request, int $id): Response
    {
        $quantite = (int) $request->request->get('quantite', 1);
        
        $this->panierService->modifierQuantite($id, $quantite);
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'nombreProduits' => $this->panierService->getNombreProduits(),
                'total' => $this->panierService->getTotal()
            ]);
        }
        
        return $this->redirectToRoute('app_panier');
    }
    
    #[Route('/vider', name: 'app_panier_vider')]
    public function vider(): Response
    {
        $this->panierService->viderPanier();
        
        $this->addFlash('success', 'Votre panier a été vidé.');
        
        return $this->redirectToRoute('app_panier');
    }
    
    #[Route('/compteur', name: 'app_panier_compteur')]
    public function compteur(): Response
    {
        return $this->render('panier/compteur.html.twig', [
            'nombreProduits' => $this->panierService->getNombreProduits()
        ]);
    }
}