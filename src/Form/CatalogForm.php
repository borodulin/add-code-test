<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Catalog;
use App\Form\Data\CatalogData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatalogForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('parent', EntityType::class, [
                'class' => Catalog::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogData::class,
            'csrf_protection' => false,
        ]);
    }
}
