<?php

namespace App\Form;

use App\DTO\ContactDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('contact_label.name', [], 'ui')
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('contact_label.email', [], 'ui')
            ])
            ->add('message', TextareaType::class, [
                'label' => $this->translator->trans('contact_label.message', [], 'ui'),
                'attr' => [
                    'rows' => 4,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('contact_label.submit', [], 'ui'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
        ]);
    }
}
