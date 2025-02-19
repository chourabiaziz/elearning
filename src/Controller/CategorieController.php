<?php


namespace App\Controller;


use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;



final class CategorieController extends AbstractController
{
   
    #[Route('/categorie', name: 'app_categorie_index')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        // Récupérer toutes les catégories de la base de données
        $categorie_liste = $categorieRepository->findAll();
    
        // Passer la liste des catégories à la vue
        return $this->render('categorie/index.html.twig', [
            'categorie_liste' => $categorie_liste,
        ]);
    }
    
    

    #[Route('/showCategorie/{id}', name: 'show_categorie')]
    public function showCategorie(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    #[Route('/addCategorie', name: 'ajouter_categorie', methods: ['GET', 'POST'])]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
    
                try {
                    // Déplacer le fichier d'image dans le répertoire spécifié
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier');
                }
                // Associer le nom de fichier à l'entité catégorie
                $categorie->setImage($newFilename);
            } else {
                $this->addFlash('error', 'L\'image est obligatoire.');
                return $this->redirectToRoute('ajouter_categorie'); // Redirigez pour afficher l'erreur
            }
    
            // Persister la catégorie dans la base de données
            $entityManager->persist($categorie);
            $entityManager->flush();
    
            // Ajout d'un message flash pour confirmation
            $this->addFlash('success', 'Catégorie ajoutée avec succès.');
    
            // Rediriger vers la liste des catégories
            return $this->redirectToRoute('app_categorie_index');
        }
    
        // Afficher la vue du formulaire
        return $this->render('categorie/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/deleteCategorie/{id}', name: 'delete_categorie', methods: ['GET'])]
    public function delete(Categorie $categorie, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier si la catégorie contient des cours
        if ($categorie->getCours()->count() > 0) {
            // Si des cours sont présents, demander une confirmation avant la suppression
            if ($request->query->get('confirm') !== 'true') {
                // Si la confirmation n'a pas été donnée, afficher un message de confirmation
                return $this->render('categorie/confirm_delete.html.twig', [
                    'categorie' => $categorie,
                ]);
            }
    
            // Si l'utilisateur confirme, dissocier les cours de cette catégorie
            foreach ($categorie->getCours() as $cours) {
                $cours->setCategorie(null);  // Dissocier la catégorie des cours
                $entityManager->persist($cours);
            }
        }
    
        // Supprimer la catégorie
        $entityManager->remove($categorie);
        $entityManager->flush();
    
        // Rediriger vers la liste des catégories après suppression
        return $this->redirectToRoute('app_categorie_index');
    }
    
    

     // Exemple de méthode dans votre contrôleur Symfony
public function new(Request $request): Response
{
    $categorie = new Categorie(); // Objet de catégorie
    $form = $this->createForm(CategorieType::class, $categorie);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Sauvegarde des données si le formulaire est valide
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categorie);
        $entityManager->flush();

        $this->addFlash('success', 'Catégorie créée avec succès.');
        return $this->redirectToRoute('app_categorie_index');
    }

    // Rendre la vue avec le formulaire
    return $this->render('categorie/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/categorie/update/{id}', name: 'update_categorie')]
    public function update($id, Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager): Response
    {
        $categorie = $categorieRepository->find($id);
    
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
    
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                if ($categorie->getImage()) {
                    $oldImagePath = $this->getParameter('image_directory') . '/' . $categorie->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Supprimer l'ancienne image
                    }
                }
    
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                    try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
    
                    $categorie->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier');
                }
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_categorie_index');
        }
    
        return $this->render('categorie/update.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie, // Ajout de la variable categorie

        ]);
    }
    


   
    #[Route('/check_nom_categorie', name: 'check_nom_categorie', methods: ['POST'])]
    public function checkNomCategorie(Request $request, CategorieRepository $categorieRepository): JsonResponse
    {
        try {
            $nom = $request->request->get('nom');
            $categorie = $categorieRepository->findOneBy(['nom' => $nom]);

            return new JsonResponse([
                'unique' => $categorie === null,
            ]);
        } catch (\Exception $e) {
            // Log l'erreur pour avoir plus d'infos
            $this->get('logger')->error('Erreur lors de la vérification du nom de la catégorie : ' . $e->getMessage());
            return new JsonResponse([
                'error' => 'Erreur interne du serveur.',
            ], 500);
        }
    }




    

    #[Route('/base', name: 'app_base')]
    public function hiba(CategorieRepository $categorieRepository): Response
    {
        // Récupérer toutes les catégories
        $categorie_liste = $categorieRepository->findAll();
    
        // Passer la liste des catégories à la vue
        return $this->render('basefront.html.twig', [
            'categorie_liste' => $categorie_liste,
        ]);
    }

    #[Route('/baseback', name: 'app_baseback')]
    public function back(): Response
    {
        return $this->render('baseback.html.twig', []);
    }
    
}


