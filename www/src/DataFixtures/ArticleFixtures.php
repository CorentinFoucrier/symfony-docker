<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i=1; $i < 11; $i++) {
            $article = new Article();
            $article->setTitle('Article #'.$i)
                    ->setContent($faker->sentence('100', true))
                    ->setImage('https://picsum.photos/960/540?random='.$i)
                    ->setCreatedAt($faker->DateTimeBetween('-6 month'));
            $manager->persist($article);
        }
        $manager->flush();
    }
}
