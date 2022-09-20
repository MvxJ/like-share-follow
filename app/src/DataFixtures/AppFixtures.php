<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $microPost1 = new MicroPost();
        $microPost1->setTitle("Welcome to Our blog!");
        $microPost1->setText("Sample text...");
        $microPost1->setCreatedAt(new \DateTime());
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle("Blog configuration!");
        $microPost2->setText("Sample text...");
        $microPost2->setCreatedAt(new \DateTime());
        $manager->persist($microPost2);

        $microPost3 = new MicroPost();
        $microPost3->setTitle("Get in touch with your friends!");
        $microPost3->setText("Sample text...");
        $microPost3->setCreatedAt(new \DateTime());
        $manager->persist($microPost3);

        $manager->flush();
    }
}
