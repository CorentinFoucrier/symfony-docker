<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i=0; $i < 5; $i++) { 
            $category = new Category();
            $category
                ->setName($faker->sentence(2, true))
                ->setDescription($faker->text())
            ;
            $manager->persist($category);
        }
        for ($j=1; $j < mt_rand(3,6); $j++) {
            $article = new Article();
            $article
                ->setTitle('Article #'.$j)
                ->setContent($faker->sentence('100', true))
                ->setImage('https://picsum.photos/960/540?random='.$j)
                ->setCreatedAt($faker->DateTimeBetween('-6 month'))
                ->setCategory($category)
            ;
            $manager->persist($article);
            for ($k=0; $k < rand(4,6); $k++) { 
                $comment = new Comment();
                $comment
                    ->setAuthor($faker->name())
                    ->setContent($faker->Text(100))
                    ->setCreatedAt($faker->dateTimeBetween('-6 month'))
                    ->setArticle($article)
                ;
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
