<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Association;
use App\Entity\ThemeAssoc;
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
            $fakeName = str_replace("[, ]+","-",$this->faker->company);
            dump($fakeName);
            array_push($fakeCompagnies[0], $fakeName);
            array_push($fakeCompagnies[1], $fakeName . "@gmail.com");
        }

        //Je mélange mon tableau de theme pour associer à une association un theme aleatoire
        $themeAssocs = $manager->getRepository(ThemeAssoc::class)->findAll();
        $arrayThemeAssoc = [];
        foreach($themeAssocs as $themeAssoc){
            array_push($arrayThemeAssoc,$themeAssoc);
        }

        //Melange mon tableau d'adresse pour l'associer à une association
        $addresses = $manager->getRepository(Address::class)->findAll();
        $arrayAddress = [];
        foreach($addresses as $address){
            array_push($arrayAddress,$address);
        }
        //Ici je crée une association et un user que je joins ensemble.
        for ($i = 0; $i < count($fakeCompagnies[0]); $i++) {
            dump("veuillez patienter... il reste " . (count($fakeCompagnies[0]) - $i) . " entrées à traité");
            $assocation = new Association();
            shuffle($arrayThemeAssoc);
            $rand_keys_theme = array_rand($arrayThemeAssoc,1);
            $assocation->setName($fakeCompagnies[0][$i])
                ->setDescription($this->faker->text(140))
                ->setTheme($arrayThemeAssoc[$rand_keys_theme])
                ->setAddress($arrayAddress[$i]);

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
            ThemeAssocFixtures::class,
        );
    }
}
