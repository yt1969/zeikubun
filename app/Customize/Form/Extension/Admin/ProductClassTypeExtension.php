<?php

namespace Customize\Form\Extension\Admin;

use Doctrine\DBAL\Types\IntegerType;
use Eccube\Form\Type\Admin\ProductClassType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductClassTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ProductClassType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zei_kubun', ChoiceType::class, [
                'choices' => [
                    '税抜' => '0',
                    '税込' => '1',
                ],
                'expanded' => true,
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]
        );
    }
}

