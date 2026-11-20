<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Produto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProdutoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome')
            ->add('descricao')
            ->add('preco')
            ->add('estoque')
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'label' => 'Categoria',
                'placeholder' => 'Selecione uma categoria',
                'choice_label' => function (Categoria $categoria): string {
                    return '#' . $categoria->getId() . ' - ' . $categoria->getNome();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produto::class,
        ]);
    }
}