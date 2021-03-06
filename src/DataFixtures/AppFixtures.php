<?php

namespace App\DataFixtures;

use App\Entity\Category;
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

        //l'ordre est important : on commence par créer nos catégories
        //le nom des 5 catégories de base qui sont toujours les mêmes
        $categoryNames = ["Travel & Adventure", "Sport", "Entertainment", "Human Relations", "Others",];
        foreach($categoryNames as $name){
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }
        $manager->flush();

        //je re-récupère toutes mes catégories depuis la bdd pour avoir de belles instances toutes propres
        $categoryRepository = $manager->getRepository(Category::class);
        $allCategories = $categoryRepository->findAll();

        // Exécute le code 50 fois
        for($i = 0; $i < 50; $i++)
        {
            // Créé un wish vide
            $wish = new Wish();

            // Hydrate le wish
            $wish->setTitle($faker->sentence);
            $wish->setDescription($faker->text);
            $wish->setAuthor($faker->userName);
            $wish->setIsPublished($faker->boolean(90));
            $wish->setDateCreated($faker->dateTimeBetween('-2 years'));
            $wish->setLikes($faker->numberBetween(0, 5000));
            $wish->setCategory($faker->randomElement($allCategories));

            // Demande à doctrine de sauvegarder ce wish
            $manager->persist($wish);
        }

        // Exécute la requête SQL
        $manager->flush();
    }
}
