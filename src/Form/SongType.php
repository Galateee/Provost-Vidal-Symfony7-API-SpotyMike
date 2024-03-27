<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\PlaylistHasSong;
use App\Entity\Song;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idSong')
            ->add('title')
            ->add('url')
            ->add('cover')
            ->add('visibility')
            ->add('createAt', null, [
                'widget' => 'single_text'
            ])
            ->add('Artist_idUser', EntityType::class, [
                'class' => Artist::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('album', EntityType::class, [
                'class' => Album::class,
'choice_label' => 'id',
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
            'data_class' => Song::class,
        ]);
    }
}
