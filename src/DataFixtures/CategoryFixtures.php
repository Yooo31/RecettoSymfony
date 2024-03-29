<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Colors\RandomColor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
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

    $categories = ['Entrée', 'Plat', 'Dessert', 'Goûter'];
    foreach ($categories as $i=>$category) {
        $newCategory = new Category();
        $newCategory->setName($category)
            ->setSlug($this->slugger->slug($category))
            ->setColor(RandomColor::one(array('luminosity' => 'bright', 'format' => 'hex')))
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));
        $manager->persist($newCategory);
        $this->addReference('CATEGORY' . $i, $newCategory);
    }

    $manager->flush();
}
}