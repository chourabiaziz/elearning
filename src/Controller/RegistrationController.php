<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegistrationController extends AbstractController
{
   /* public function __construct(private EmailVerifier $emailVerifier)
    {
    }*/

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager // Inject the upload directory path
    ): Response
    {
        $errors = null; // Initialize errors as null or an empty array
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user,  [
            
        ]);
        $form->handleRequest($request);

        
        if ($form->isSubmitted()) {


            // Validate the user entity (will check the constraints defined in the User class)
            //$errors = $validator->validate($user);
            $errors = $form->getErrors(true, false);

            if (count($errors) > 0) {
                // If there are errors, return the errors to the template
                return $this->render('registration/register.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                ]);
            }
            

            

            if ($form->isValid()) {
                // Handle the logic for storing the user
                $user->setRoles(['ROLE_STUDENT']);
                $user->setStatus('ACTIVE');
                $user-> setExperiences('null');
                $user->setLanguages('null');

                // file upload
                 /** @var UploadedFile $imageFile */
            $imageFile = $form->get('photo')->getData(); 

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier');
                    return $this->redirectToRoute('app_register');
                }

                $user->setPhoto($newFilename);
            } else {
                $user->setPhoto('anonymous.jpg');
            }

                $user->setDateCreate(new \DateTime());
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('list_user');
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,

        ]);
    }
    

   
    
/*
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {*/
            /** @var User $user */
            /*
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
        */



        #[Route('/registerProf', name: 'app_registerProf')]
        public function registerProf(
            Request $request, 
            UserPasswordHasherInterface $userPasswordHasher, 
            EntityManagerInterface $entityManager, 
            ValidatorInterface $validator
        ): Response {
            $user = new User();
            $formProf = $this->createForm(RegistrationFormType::class, $user,  [
                 
            ]);
            $formProf->handleRequest($request);
        
            if ($formProf->isSubmitted()) {
                // Validate the user entity
                $errors = $validator->validate($user);
        
                if (count($errors) > 0) {
                    // Return validation errors to the template
                    return $this->render('registration/registerProf.html.twig', [
                        'formProf' => $formProf->createView(),
                        'errors' => $errors,
                    ]);
                }
        
                if ($formProf->isValid()) {
                    $user->setStatus('INACTIVE');
                    $roles[] = 'ROLE_CREATOR';
                    $user->setRoles(array_unique($roles));
                    $user->setDateCreate(new \DateTime());
        
                    // Handle file upload
                    
        
                    // Encode the password
                    $plainPassword = $formProf->get('plainPassword')->getData();
                    $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
        
                    $entityManager->persist($user);
                    $entityManager->flush();
        
                    return $this->redirectToRoute('list_prof');
                }
            }
        
            return $this->render('registration/registerProf.html.twig', [
                'formProf' => $formProf->createView(),
            ]);
        }
        


        
     
        
   



     
    
    
}