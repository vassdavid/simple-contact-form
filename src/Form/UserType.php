<?php

namespace App\Form;

use App\DTO\UserDTO;
use App\Constant\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordRequired = in_array('create', $options['validation_groups'], true);
        $builder
            ->add('username', TextType::class, [
                'label' => $this->translator->trans('user.username', [], 'ui')
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => UserRole::ROLE_ADMIN
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => $this->translator->trans('user.roles', [], 'ui'),
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => $passwordRequired,
                'first_options' => [
                    'label' => $this->translator->trans('user.password', [], 'ui'),
                ],
                'second_options' => [
                    'label' =>  $this->translator->trans('user.password_again', [], 'ui'),
                ],
            ]);
        if(in_array('create', $options['validation_groups'], true)) {
            $builder
                ->add('submit', SubmitType::class, [
                    'label' => $this->translator->trans('user.create', [], 'ui'),
                ]);
        }
        elseif(in_array('edit', $options['validation_groups'], true)) {
            $builder
                ->add('submit', SubmitType::class, [
                    'label' => $this->translator->trans('user.edit', [], 'ui'),
                ]);
        }
   
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDTO::class,
        ]);
    }
}
