<?php
namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class PagesController extends AbstractController
{
     #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/pages', name: 'app_index')]
    public function index(): Response
    {
        // Définissez vos produits directement dans un tableau
        $produits = [
            [
                'id' => 1,
                'nom' => 'Poterie tunisienne',
                'description' => 'Magnifique poterie artisanale de Nabeul',
                'prix' => 45.99,
                'image' => 'poterie.jpg'
            ],
            [
                'id' => 2,
                'nom' => 'Tapis berbère',
                'description' => 'Tapis traditionnel tissé à la main',
                'prix' => 120.50,
                'image' => 'tapis.jpg'
            ],
            [
                'id' => 3,
                'nom' => 'Caftan tunisien',
                'description' => 'Caftan traditionnel brodé main',
                'prix' => 89.99,
                'image' => 'caftan.jpg'
            ],
            [
                'id' => 4,
                'nom' => 'Plateau en cuivre',
                'description' => 'Plateau artisanal gravé en cuivre',
                'prix' => 35.75,
                'image' => 'plateau.jpg'
            ]
        ];
        
        return $this->render('pages/index.html.twig', [
            'produits' => $produits,
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('about.html.twig'); // À la racine de templates/
    }

    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('connexion.html.twig', [ // À la racine de templates/
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function inscription(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method will be intercepted by the logout key on your firewall.');
    }
}