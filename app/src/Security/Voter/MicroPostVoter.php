<?php

namespace App\Security\Voter;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MicroPostVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [MicroPost::EDIT, MicroPost::VIEW])
            && $subject instanceof MicroPost;
    }

    /**
     * @param string $attribute
     * @param MicroPost $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        $isAuthenticated = $user instanceof UserInterface;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case MicroPost::EDIT:
                return $isAuthenticated &&
                    (
                        ($subject->getAuthor()->getId() === $user->getId()) ||
                        $this->security->isGranted('ROLE_EDITOR')
                    );
            case MicroPost::VIEW:
                return true;
        }

        return false;
    }
}