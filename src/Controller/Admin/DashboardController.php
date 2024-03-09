<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Contract;
use App\Entity\Formation;
use App\Entity\User;
use App\Repository\ContractRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'app_admin')]
    public function __invoke(ContractRepository $contractRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'contractsCompany' => $contractRepository->countByCompany(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App')
            ->generateRelativeUrls()
            ->renderContentMaximized();
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Configuration');
        yield MenuItem::linkToCrud('Formation', 'fas fa-icons', Formation::class);
        yield MenuItem::linkToCrud('Company', 'fas fa-building', Company::class);

        yield MenuItem::section('Contracts');
        yield MenuItem::linkToCrud('Contract', 'fas fa-building', Contract::class);

        yield MenuItem::section('Security');
        yield MenuItem::linkToCrud('User', 'fas fa-users', User::class);
    }
}
