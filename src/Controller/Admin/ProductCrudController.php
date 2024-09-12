<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')

        ;
    }


    public function configureFields(string $pageName): iterable
    {
        $required=true;

        if ($pageName =='edit'){
            $required = false;
        }

        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom du Produit'),
            BooleanField::new('isHomepage')->setLabel('Produit à la une ?')->setHelp('Affiche le produit sur la page principale du site'),
            SlugField::new('slug')->setTargetFieldName('name')->setLabel('URL')->setHelp('URL du produit généré automatiquement'),
            TextEditorField::new('description')->setLabel('Description'),
            ImageField::new('illustration')->setLabel('Image')->setHelp('Image du produit')
            ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
            ->setBasePath('/uploads')
            ->setUploadDir('/public/uploads')
            ->setRequired($required),
            NumberField::new('price')->setLabel('Prix H.T'),
            ChoiceField::new('tva')->setLabel('Taxe %')->setChoices([
                '5.5%'=>'5.5',
                '10%'=>'10',
                '20%'=>'20'
            ]),
            AssociationField::new('category', 'Catégorie Associée')
        ];
    }

}
