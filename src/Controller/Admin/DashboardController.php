<?php

namespace App\Controller\Admin;

use Override;
use App\Entity\Company;
use App\Entity\Contract;
use App\Entity\Formation;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\ContractRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly CompanyRepository $companyRepository
    ) {
    }

    #[Route('/admin', name: 'app_admin')]
    public function __invoke(ContractRepository $contractRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'contractsCompany' => $contractRepository->countByCompany(),
        ]);
    }

    #[Override]
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App')
            ->generateRelativeUrls()
            ->renderContentMaximized();
    }

    #[Override]
    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    #[Override]
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Configuration');
        yield MenuItem::subMenu('Formations', 'fa fa-icons ')
            ->setSubItems(
                $this->subFormations()
            );

        yield MenuItem::linkToCrud('Company', 'fas fa-building', Company::class);

        yield MenuItem::section('Contracts');
        yield MenuItem::linkToCrud('Contract', 'fas fa-building', Contract::class);

        yield MenuItem::section('Security');
        yield MenuItem::linkToCrud('User', 'fas fa-users', User::class);

        yield MenuItem::section();
        yield MenuItem::linkToLogout('Logout', 'fa fa-right-from-bracket');
    }

    /**
     * @return array<MenuItemInterface>
     */
    private function subFormations(): array
    {
        $sub = [MenuItem::linkToCrud('All', 'fas fa-icons', Formation::class)];

        $companies = $this->companyRepository->findAll();
        foreach ($companies as $company) {
            $sub[] = MenuItem::linkToCrud((string) $company->getName(), 'fas fa-angles-right', Formation::class)
                ->setController(FormationCrudController::class)
                ->setAction('index')
                ->setQueryParameter('filters[company][comparison]', '=')
                ->setQueryParameter('filters[company][value]', $company->getId());
        }

        return $sub;
    }
}
