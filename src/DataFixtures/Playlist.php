<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Playlist as EntityPlaylist;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Playlist extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 6; $i++) { 
           
            $user = new EntityUser();
            $user->setFirstName("User_".rand(0,999));
            $user->setLastName("User_".rand(0,999));
            $user->setSexe("User_".rand(0,999));
            $user->setBirthDate(new DateTimeImmutable());
            $user->setEmail("User_".rand(0,999));
            $user->setIdUser("User_".rand(0,999));
            $user->setCreateAt(new DateTimeImmutable());
            $user->setUpdateAt(new DateTimeImmutable()); 
            $user->setPassword("$2y$".rand(0,999999999999999999));
            $manager->persist($user);
        }
        for ($i=0; $i < 6; $i++) { 
           
            $playlist = new EntityLabel();
            $playlist->setTitle("Playlist".rand(0,999));
            $playlist->setPublic("Playlist".rand(0,999));
            $playlist->setCreateAt(new DateTimeImmutable());
            $playlist->setUpdateAt(new DateTimeImmutable());
            $playlist->setIdPlaylist($user);
            $manager->persist($playlist);   
        }
        $manager->flush();
    }
}
