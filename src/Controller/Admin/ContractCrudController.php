<?php

namespace App\Controller\Admin;

use App\Entity\Contract;
use App\Entity\Enum\LocationEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContractCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contract::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $contractPDF = Action::new('contractPDF', 'PDF')
            ->linkToRoute('app_contract_pdf', static fn (Contract $contract): array => ['id' => $contract->getId()])
            ->setHtmlAttributes(['target' => '_blank'])
        ;

        $contract = Action::new('contract', 'Contract')
            ->linkToRoute('app_contract', static fn (Contract $contract): array => ['id' => $contract->getId()])
            ->setHtmlAttributes(['target' => '_blank'])
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $contractPDF)
            ->add(Crud::PAGE_DETAIL, $contractPDF)
            ->add(Crud::PAGE_INDEX, $contract)
            ->add(Crud::PAGE_DETAIL, $contract)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(8);

        yield IdField::new('id')->hideOnForm();
        yield FormField::addFieldset('Main Information');
        yield TextField::new('contractor');
        yield MoneyField::new('amount')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->hideOnForm();
        yield AssociationField::new('formation');

        yield ChoiceField::new('location')
            ->formatValue(static fn (LocationEnum $value, Contract $entity) => $entity->getLocation()?->value);

        yield FormField::addFieldset('Period Information');
        yield Field::new('duration')->hideOnForm();
        yield Field::new('days')->onlyOnDetail()->formatValue(static fn ($value) => sprintf('%d days', $value));
        yield DateField::new('startAt')->setColumns(6);
        yield DateField::new('endAt')->setColumns(6);

        yield FormField::addColumn(4);
        yield FormField::addFieldset('Context')->onlyOnDetail();
        yield AssociationField::new('author')->onlyOnDetail();
        yield DateTimeField::new('createdAt')->onlyOnDetail();
        yield DateTimeField::new('updatedAt')->onlyOnDetail();

        yield FormField::addFieldset('Media')->onlyOnDetail();
        yield TextField::new('fileContract')->hideOnForm();
    }
}
