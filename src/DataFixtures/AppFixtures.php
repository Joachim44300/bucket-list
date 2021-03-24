<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        // Créer notre Faker pour générer de belles données aléatoires
        $faker = \Faker\Factory::create();

        // Exécute le code 200 fois
        for($i = 0; $i < 200; $i++)
        {
            // Créé un wish vide
            $wish = new Wish();

            // Hydrate le wish
            $wish->setTitle($faker->sentence);
            $wish->setDescription($faker->text);
            $wish->setAuthor($faker->userName);
            $wish->setIsPublished($faker->boolean(90));
            $wish->setDateCreated($faker->dateTimeBetween('-2 years'));

            // Demande à doctrine de sauvegarder ce wish
            $manager->persist($wish);
        }

        // Exécute la requête SQL
        $manager->flush();
    }
}
