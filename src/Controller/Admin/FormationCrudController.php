<?php

namespace App\Controller\Admin;

use App\Entity\Enum\TemplateEnum;
use App\Entity\Formation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FormationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('company')
            ->add('price')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(8);

        yield IdField::new('id')->hideOnForm();
        yield FormField::addFieldset('Main Information');
        yield TextField::new('name');
        yield AssociationField::new('company');
        yield Field::new('duration');
        yield TextEditorField::new('description');
        yield MoneyField::new('price')->setCurrency('EUR')->setStoredAsCents(false);
        yield ChoiceField::new('template')
            ->setLabel('PDF template')
            ->formatValue(static fn (TemplateEnum $value, Formation $entity) => $value->value);

        yield FormField::addColumn(4);
        yield FormField::addFieldset('Context')->onlyOnDetail();
        yield DateField::new('createdAt')->hideOnForm();
        yield DateField::new('updatedAt')->onlyOnDetail();
    }
}
