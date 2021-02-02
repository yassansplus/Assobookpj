<?php

namespace App\DataFixtures;

use App\Entity\Association;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AssociationsFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        // facompagnie premiere case Nom de l'entreprise, deuxieme email
        $fakeCompagnies = [[], []];

        //Ici je génère toutes les associations dont j'ai besoin avec un nom et une email egale à NOM@gmail.com
        for ($i = 0; $i < 30; $i++) {
            $fakeName = $this->faker->company;
            array_push($fakeCompagnies[0], $fakeName);
            array_push($fakeCompagnies[1], str_replace(' ', '', $fakeName) . "@gmail.com");
        }

        //Ici je crée une association et un user que je joins ensemble.
        for ($i = 0; $i < count($fakeCompagnies[0]); $i++) {
            dump("veuillez patienter... il reste " . (count($fakeCompagnies[0]) - $i) . " entrées à traité");
            $assocation = new Association();
            $assocation->setName($fakeCompagnies[0][$i])
                ->setDescription($this->faker->text(140))
                ->setLocation($this->faker->city);


            $user = new User();
            $user->setEmail($fakeCompagnies[1][$i])
                ->setPassword($this->encoder->encodePassword($user, '123'))
                ->setAssociation($assocation)
                ->setRoles(['ROLE_ASSOC_CONFIRME']);

            $manager->persist($assocation);
            $manager->persist($user);
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
