<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/accounts')]
#[IsGranted('ROLE_USER')]
final class AccountController extends AbstractController
{
    #[Route('', name: 'app_accounts', methods: ['GET'])]
    public function index(AccountRepository $accountRepository): Response
    {
        $user = $this->getUser();

        // Si es admin, ve todas las cuentas. Si no, solo las suyas.
        if ($this->isGranted('ROLE_ADMIN')) {
            $accounts = $accountRepository->findAll();
        } else {
            $accounts = $accountRepository->findBy(['owner' => $user]);
        }

        return $this->render('account/index.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    #[Route('/{id}', name: 'app_account_show', methods: ['GET'])]
    public function show(Account $account): Response
    {
        // El Voter se encarga de verificar que el usuario puede ver esta cuenta
        $this->denyAccessUnlessGranted('ACCOUNT_VIEW', $account);

        return $this->render('account/show.html.twig', [
            'account' => $account,
        ]);
    }
}