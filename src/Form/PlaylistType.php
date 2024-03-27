<?php

namespace App\Form;

use App\Entity\Playlist;
use App\Entity\PlaylistHasSong;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idPlaylist')
            ->add('title')
            ->add('public')
            ->add('createAt', null, [
                'widget' => 'single_text'
            ])
            ->add('updateAt', null, [
                'widget' => 'single_text'
            ])
            ->add('playlistHasSong', EntityType::class, [
                'class' => PlaylistHasSong::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
