<?php

namespace App\Controller\Admin;

use App\Entity\Contract;
use App\Entity\Enum\LocationEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContractCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contract::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('contractor');
        yield MoneyField::new('amount')
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->hideOnForm();
        yield AssociationField::new('formation');
        yield TextField::new('fileContract')->hideOnForm();

        yield ChoiceField::new('location')
            ->formatValue(static fn (LocationEnum $value, Contract $entity) => $entity->getLocation()?->value);
        yield Field::new('duration')->hideOnForm();

        yield DateField::new('startAt');
        yield DateField::new('endAt');
        yield DateTimeField::new('createdAt')->onlyOnDetail();
        yield DateTimeField::new('updatedAt')->onlyOnDetail();
    }
}
