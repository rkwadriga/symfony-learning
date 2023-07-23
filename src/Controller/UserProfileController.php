<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User getUser()
 */
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function profile(Request $request, UserRepository $repository): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile() ?? new UserProfile();
        $form = $this->createForm(UserProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setProfile($form->getData());
            $repository->save($user, true);
            $this->addFlash('success', 'Your Profile was successfully changed');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
