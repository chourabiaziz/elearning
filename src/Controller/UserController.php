<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class UserController extends AbstractController
{

    
   
    #[Route('/signin', name: 'ajouter_user', methods:  ['GET', 'POST'])]
    
    public function ajouter(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);  

            $user->setRoles(['ROLE_STUDENT']);
            $user->setStatus('ACTIVE');

            // Set the current date and time for account creation
            $user->setDateCreate(new \DateTime());

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('photo')->getData(); 

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Move the uploaded file to the 'uploads' directory
                    $imageFile->move(
                        $this->getParameter('upload_directory'), // Defined in services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload errors
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier');
                    return $this->redirectToRoute('ajouter_user'); // Redirect back if error
                }

                // Store the filename in the 'photo' field of the user entity
                $user->setPhoto($newFilename);
            } else {
                // Set default photo if no file is uploaded
                $user->setPhoto('default.jpg');
            }

            // Persist the user entity to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Flash success message and redirect
            $this->addFlash('success', 'User ajouté avec succès!');
            return $this->redirectToRoute(route: 'list_user');
        }

        // Render the form view
        return $this->render('user/ajouterUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    

    #[Route("/deleteUser/{id}", name: "deleteUser")]
    public function delete($id, UserRepository $UserRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $UserRepository->find($id);

        if ($user) {
             // Store the user's roles before deletion
             $roles = $user->getRoles();

            $entityManager->remove($user);
            $entityManager->flush();
 
    
            // Flash success message
            $this->addFlash('success', 'user deleted !');
        } else {
            $this->addFlash('error', 'user not found');
        }

        if (in_array('ROLE_STUDENT', $roles)) {
            return $this->redirectToRoute('list_user');
        } elseif (in_array('ROLE_CREATOR', $roles)) {
            return $this->redirectToRoute('list_prof');  // Replace with the actual route for professors
        } 
    }



   
     
    
    


    #[Route('/user/update/{id}', name: 'update_user')]
    public function update($id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        // Vérification si l'événement existe
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Créer le formulaire pour l'événement
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification si une nouvelle image a été téléchargée
            $imageFile = $form->get('photo')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Déplacer l'image dans le dossier public/image
                    $imageFile->move(
                        $this->getParameter('upload_directory'), // Utilisation du paramètre upload_directory
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion d'erreur lors du téléchargement
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                    return $this->redirectToRoute('update_user', ['id' => $id]); // Rediriger vers la page de mise à jour
                }

                // Mettre à jour l'image de l'événement avec le nouveau nom de fichier
                $user->setPhoto($newFilename);
            }

            // Persister la mise à jour de l'événement dans la base de données
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'User updated!');

            // Rediriger vers la liste des événements
            return $this->redirectToRoute('list_user');
        }

        return $this->render('user/updateUser=.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }



    #[Route('/basefront', name: 'app_basefront')]
    public function index(): Response
    {
        return $this->render('basefront.html.twig', []);
    }

    #[Route('/baseback', name: 'app_baseback')]
    public function back(): Response
    {
        return $this->render('baseback.html.twig', []);
    }
    
 
    #[Route('/userListAdmin', name: 'app_listAdmin')]
    public function userListAdmin(): Response
    {
        return $this->render('admin/usersList.html.twig', []);
    }
    
 
    #[Route('/login', name: 'app_user_login')]
    public function login(): Response
    {
        return $this->render('User/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/listuser', name: 'list_user')]
    public function showUser(UserRepository $rep): Response
    {
        $list = $rep->createQueryBuilder('u')
                    ->where('u.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_STUDENT"%')
                    ->getQuery()
                    ->getResult();
    
        return $this->render('admin/usersList.html.twig', [
            'user_liste' => $list,
        ]);
    }
   
    #[Route('/user/{id}/setInactive', name: 'set_inactive')]
    public function setInactive($id, EntityManagerInterface $entityManager): RedirectResponse
    {
        
        // Retrieve the user by their ID
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Set the user's status to inactive
        $user->setStatus('INACTIVE');
        $entityManager->flush();

        $roles = $user->getRoles();
        if (in_array('ROLE_CREATOR', $roles, true)) {
            return $this->redirectToRoute('list_prof');
        } elseif (in_array('ROLE_STUDENT', $roles, true)) {
            return $this->redirectToRoute('list_user');
        }

 
    }

    #[Route('/user/{id}/setActive', name: 'set_active')]
    public function setactive($id, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Retrieve the user by their ID
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Set the user's status to inactive
        $user->setStatus('ACTIVE');
        $entityManager->flush();

        $roles = $user->getRoles();
        if (in_array('ROLE_CREATOR', $roles, true)) {
            return $this->redirectToRoute('list_prof');
        } elseif (in_array('ROLE_STUDENT', $roles, true)) {
            return $this->redirectToRoute('list_user');
        }
    }

    #[Route('/listProf', name: 'list_prof')]
    public function showProf(UserRepository $rep): Response
    {
        
        $list = $rep->createQueryBuilder('u')
        ->where('u.roles LIKE :role')
        ->setParameter('role', '%"ROLE_CREATOR"%')
        ->getQuery()
        ->getResult();

        return $this->render('admin/creatorsList.html.twig', [
            'list_prof' => $list,
        ]);
    }

    


    
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('admin/profile.html.twig', []);
    }

    
}