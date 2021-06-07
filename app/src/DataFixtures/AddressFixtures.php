<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class AddressFixtures extends Fixture
{
    private $faker;
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        for ($i=0;$i<30;$i++) {
            $address = (new Address())
                ->setCity($this->faker->city)
                ->setPostalCode($this->faker->numberBetween(10000,98000))
                ->setStreet($this->faker->streetAddress)
                ->setRegion($this->faker->state)
                ->setCountry($this->faker->country)
                ->setLatitude($this->faker->latitude)
                ->setLongitude($this->faker->longitude);
            $manager->persist($address);
        }
        $manager->flush();
    }
}