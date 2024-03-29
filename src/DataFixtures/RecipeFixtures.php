<?php

namespace App\DataFixtures;

use App\Entity\Category;
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
