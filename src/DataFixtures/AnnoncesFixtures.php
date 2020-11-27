<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Users;
use App\Entity\Annonces;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AnnoncesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 30; $i++){
            $annonce = new Annonces();
            $annonce
                ->setTitle($faker->words(3, true))
                ->setContent($faker->sentences(3, true))
                ->setActive(true)
                ->setPrice($faker->numberBetween(10, 200000));
                
                $manager->persist($annonce);
                
            }
        

        $manager->flush();
    }

}

