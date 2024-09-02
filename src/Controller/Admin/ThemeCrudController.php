<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Form\ThemeCategoryType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ThemeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Theme::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $sysgroups = AssociationField::new('systemGroups');
        $repgroups = AssociationField::new('reportGroups');

        $categoriesField = CollectionField::new('themeCategories')
            ->setEntryType(ThemeCategoryType::class)
            ->setFormTypeOptions([
                'by_reference' => false // important for OneToMany associations
            ]);


        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, ];
        }
        elseif(Crud::PAGE_DETAIL === $pageName) {
            return [$id];
        }
        elseif(Crud::PAGE_NEW === $pageName) {
            return [$name, $sysgroups, $repgroups, $categoriesField ];
        }
//        elseif(Crud::PAGE_EDIT === $pageName) {
//            return [$title, $editor_description, $editor_instructions, $editor_preparations, $coll_questions, $coll_configuration ];
//        }
    }

}
