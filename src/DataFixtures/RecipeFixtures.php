<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use Colors\RandomColor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $ingredients = array_map(fn(string $name) => (new Ingredient())
            ->setName($name)
            ->setSlug(strtolower($this->slugger->slug($name))), [
            "Farine",
            "Sucre",
            "Oeuf",
            "Lait",
            "Beurre",
            "Sel",
            "Poivre",
            "Huile",
            "Levure",
            "Chocolat",
            "Vanille",
            "Cannelle",
            "Miel",
            "Crème",
            "Pomme",
            "Poire",
            "Banane",
            "Fraise",
            "Framboise",
            "Cerise",
            "Oignon",
            "Ail",
            "Échalote",
            "Herbes fraîches"
        ]);
        $units = [
            "g",
            "kg",
            "ml",
            "cl",
            "l",
            "c. soupe",
            "c. café",
            "pincée",
            "verre"
        ];

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        for ($i = 0; $i < 20; $i++) {
            $title = $faker->foodName();
            $recipe = new Recipe();
            $recipe->setTitle($title)
                ->setSlug($this->slugger->slug($recipe->getTitle()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraph(10, true))
                ->setCategory($this->getReference('CATEGORY' . $faker->numberBetween(0, 3)))
                ->setDuration($faker->numberBetween(5, 120))
                ->setUser($this->getReference('USER' . $faker->numberBetween(1, 10)));

            foreach($faker->randomElements($ingredients, $faker->numberBetween(2, 5)) as $ingredient) {
                $recipe->addQuantity((new Quantity())
                    ->setQuantity($faker->randomFloat(1, 250))
                    ->setUnit($faker->randomElement($units))
                    ->setIngredient($ingredient)
                );
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class
        ];
    }
}
