<?php

namespace App\DataFixtures;

use App\Entity\Association;
use App\Entity\Publication;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class PublicationFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(UserPasswordEncoderInterface $encoder)
    {

    }

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $associations = $manager->getRepository(Association::class)->findAll();
        foreach ($associations as $association) {
            for ($i = 0; $i<12; $i++){
                $publication = new Publication();
                $publication->setAssociation($association);
                $publication->setDescription($this->faker->text(220));
                $publication->setDatePublication();
                $manager->persist($publication);
            }

        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return array(
            TagsFixtures::class,
        );
    }
}
