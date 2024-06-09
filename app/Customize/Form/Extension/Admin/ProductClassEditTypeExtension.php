<?php

namespace Customize\Form\Extension\Admin;

use Doctrine\DBAL\Types\IntegerType;
use Eccube\Form\Type\Admin\ProductClassEditType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;

class ProductClassEditTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ProductClassEditType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zei_kubun', ChoiceType::class, [
                'choices' => [
                    '税抜' => '0',
                    '税込' => '1',
                ],
                'expanded' => false,
                'required' => false,
                'mapped' => true,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();
                if (!$form['checked']->getData()) {
                    // チェックがついていない場合はバリデーションしない.
                    return;
                }
                if (empty($data['zei_kubun'])) {
                    $form['zei_kubun']->addError(new FormError(trans('選択してください')));
                }
            });
    }
}

