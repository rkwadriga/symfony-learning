<?php declare(strict_types=1);
/**
 * Created 2023-07-06 15:08:31
 * Author rkwadriga
 */

namespace App\Security;

use DateTime;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @param User $user
     * @throws AccessDeniedException
     */
    public function checkPreAuth(UserInterface $user)
    {
        $this->checkIsBanned($user);
    }

    /**
     * @param User $user
     * @throws AccessDeniedException
     */
    public function checkPostAuth(UserInterface $user)
    {
        //
    }

    private function checkIsBanned(User $user): void
    {
        if ($user->getBannedUntil() !== null && $user->getBannedUntil() > new DateTime()) {
            throw new AccessDeniedHttpException('The user is banned');
        }
    }
}