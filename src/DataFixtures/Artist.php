<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Artist as EntityArtist;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Artist extends Fixture
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
           
            $artist = new EntityArtist();
            $artist->setFirstName("Artist_".rand(0,999));
            $artist->setLastName("Artist_".rand(0,999));
            $artist->setSexe("Artist_".rand(0,999));
            $artist->setBirthDate(new DateTimeImmutable());
            $artist->setlabel("Artist_".rand(0,999));
            $artist->setUserIdUser($user);
            $manager->persist($artist);
        }
        $manager->flush();
    }
}
