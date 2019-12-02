<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\ArticleLike;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $user = new User();
        $users = [];
        $user
            ->setEmail('demo@demo.com')
            ->setUsername('demo')
            ->setPassword($this->encoder->encodePassword($user, 'demo'))
        ;
        $manager->persist($user);
        $users[] = $user;
        for ($i=0; $i < 30; $i++) { 
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setUsername($faker->name())
                ->setPassword($this->encoder->encodePassword($user, 'demo'))
            ;
            $manager->persist($user);
            $users[] = $user;
        }

        for ($i=0; $i < 5; $i++) { 
            $category = new Category();
            $category
                ->setName($faker->sentence(2, true))
                ->setDescription($faker->text())
            ;
            $manager->persist($category);
        }
        for ($j=1; $j < 7; $j++) {
            $article = new Article();
            $article
                ->setTitle($faker->words(2, true))
                ->setContent($faker->sentence('100', true))
                ->setImage('https://picsum.photos/960/540?random='.$j)
                ->setCreatedAt($faker->DateTimeBetween('-6 month'))
                ->setCategory($category)
                ->setPrice($faker->randomFloat(2, 15, 300))
            ;
            $manager->persist($article);
            for ($k=0; $k < mt_rand(0,10); $k++) { 
                $like = new ArticleLike();
                $like
                    ->setArticle($article)
                    ->setUsers($faker->randomElement($users))
                ;
                $manager->persist($like);
            }
            for ($l=0; $l < rand(4,6); $l++) { 
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
