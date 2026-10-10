<?php

namespace App\Security\Voter;

use App\Entity\Account;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class AccountVoter extends Voter
{
    public const VIEW = 'ACCOUNT_VIEW';
    public const EDIT = 'ACCOUNT_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Account;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Los administradores pueden ver y editar cualquier cuenta
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        /** @var Account $account */
        $account = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($account, $user),
            self::EDIT => $this->canEdit($account, $user),
            default => false,
        };
    }

    private function canView(Account $account, User $user): bool
    {
        // Solo el dueño puede ver su cuenta
        return $account->getOwner() === $user;
    }

    private function canEdit(Account $account, User $user): bool
    {
        // Solo el dueño puede editar su cuenta
        return $account->getOwner() === $user;
    }
}