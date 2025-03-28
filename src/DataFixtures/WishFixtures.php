<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class WishFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i=0;$i<500;$i++) {
            $wish = new Wish();
            $wish->setTitle($faker->sentence(6));
            $wish->setDescription($faker->text);
            $wish->setAuthor($faker->name);
            $wish->setIsPublished($faker->boolean(90));
            $wish->setDateCreated($faker->dateTimeBetween('-6 months'));
            $wish->setDateUpdated($faker->dateTimeBetween($wish->getDateCreated()));

            $manager->persist($wish);
        }
        $manager->flush();
    }

}
