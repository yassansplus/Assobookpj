<?php

namespace App\DataFixtures;

use App\Entity\ThemeAssoc;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ThemeAssocFixtures extends Fixture
{
    private $faker;
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $fakeThemes = $this->faker->words(5,  false);
        $fakeThemes = array_unique($fakeThemes);
        foreach ($fakeThemes as $fakeTheme ) {
            $theme = (new ThemeAssoc())
                ->setName($fakeTheme);
            $manager->persist($theme);
        }
        $manager->flush();
    }
}