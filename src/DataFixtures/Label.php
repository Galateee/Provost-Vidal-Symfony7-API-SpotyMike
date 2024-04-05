<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Label as EntityLabel;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Label extends Fixture
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
           
            $label = new EntityLabel();
            $label->setName("Label".rand(0,999));
            $label->setYear(new DateTimeImmutable());
            $label->setIdLabel($user);
            $manager->persist($album);   
        }
        $manager->flush();
    }
}
