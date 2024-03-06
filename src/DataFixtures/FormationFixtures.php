<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FormationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $formation = new Formation();
        $formation->setName('Ma formation');
        $formation->setDescription('Description de ma formation');
        $formation->setPrice(1000);

        $manager->persist($formation);
        $manager->flush();

        $formation = new Formation();
        $formation->setName('Estetique');
        $formation->setDescription('Description de ma formation');
        $formation->setPrice(1500);

        $manager->persist($formation);
        $manager->flush();
    }
}
