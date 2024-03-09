<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(8);

        yield FormField::addFieldset('IDs Informations');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('activityNumber')->hideOnIndex();
        yield TextField::new('rcs')->hideOnIndex();

        yield FormField::addFieldset('Main Information');
        yield TextField::new('name');
        yield EmailField::new('email');
        yield TelephoneField::new('phone');
        yield TextField::new('representative')->hideOnIndex();

        yield AssociationField::new('formations')->hideOnForm();

        yield FormField::addFieldset('Address Information');
        yield TextField::new('address');
        yield TextField::new('postcode')->setColumns(4);
        yield TextField::new('city')->setColumns(8);
        yield FormField::addColumn(4);

        yield FormField::addFieldset('Media');

        $image = ImageField::new('picture')
            ->setUploadDir('public/uploads/pictures')
            ->setBasePath('uploads/pictures')
            ->setUploadedFileNamePattern('[year]/[month]/[day]/[slug]-[contenthash].[extension]');

        if (Action::EDIT == $pageName) {
            $image->setFormTypeOptions(['allow_delete' => false])
                ->setRequired(false);
        }

        yield $image;

        yield FormField::addFieldset('Context')->onlyOnDetail();
        yield DateField::new('createdAt')->hideOnForm();
        yield DateField::new('updatedAt')->onlyOnDetail();
    }
}
