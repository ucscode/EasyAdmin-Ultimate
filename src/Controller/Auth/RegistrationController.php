<?php

namespace App\Controller\Auth;

use App\Context\EauContext;
use App\Controller\Auth\Abstracts\AbstractAuth;
use App\Entity\User;
use App\Form\Auth\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\ConfigurationService;
use App\Utils\Stateful\BsModal\BsModal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Ucscode\KeyGenerator\KeyGenerator;

class RegistrationController extends AbstractAuth
{
    protected KeyGenerator $keyGenerator;

    public function __construct(private EmailVerifier $emailVerifier, protected ConfigurationService $configurationService, protected EauContext $eauContext)
    {
        $this->keyGenerator = new KeyGenerator();
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Generate Uniqid
            $user->setUniqueId($this->keyGenerator->generateKey(7));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            if($this->configurationService->get('user.email.send_validation_link')) {
                $this->emailVerifier->sendRegistrationVerificationEmail($user);
            }

            // do anything else you need here, like send an email
            $this->eauContext->addModal(new BsModal('Your registration was successful'), true);
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/registration/register.html.twig', [
            'registrationForm' => $form,
            'page_title' => sprintf('%s | %s', $this->configurationService->get('app.name'), 'Registeration'),
            'favicon_path' => $this->configurationService->get('app.logo', $this->favicon),
            'header_title' => 'Register Now',
            'header_logo' => $this->configurationService->get('app.logo'),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
