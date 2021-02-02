<?php

namespace App\DataFixtures;

use App\Entity\Association;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class TagsAssociationsFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $associations = $manager->getRepository(Association::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();
        foreach ($associations as $association) {
            for ($i=0; $i<rand(0, count($tags)); $i++){
            $association->getUserAccount()->addTag($tags[$i]);
            }
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return array(
            TagsFixtures::class,
            AssociationsFixtures::class
        );
    }
}
