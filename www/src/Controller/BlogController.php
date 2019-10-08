<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(CacheInterface $cache)
    {

        $articles = $cache->getCache('article', function(ItemInterface $item) {
            $item->expireAfter(10);
            $articleRepo = $this->getDoctrine()->getRepository(Article::class);
            return $articleRepo->findAll();
        });

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    public function show(Article $article, ObjectManager $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si pas d'ID on créer
            if (!$comment->getId()) {
                $comment
                    ->setCreatedAt(new \DateTime())
                    ->setArticle($article)
                ;
            }
            $manager->persist($comment);
            $manager->flush();
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'formComment' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_new")
     * @Route("/blog/edit/{id}", name="blog_edit")
     */
    public function form(Request $request, ObjectManager $manager, Article $article = null)
    {
        if (!$article) {
            $article = new Article();
            $title = "Créer l'article";
        } else {
            $title = "Editer l'article";
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }
            $manager->persist($article);
            $manager->flush();
            $this->addFlash('success', 'L\'article a bien été créé');
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/form.html.twig', [
            'form' => $form->createView(),
            'title' => $title,
        ]);
    }

    /**
     * @Route("/blog/delete/{id}", name="blog_delete")
     */
    public function delete(Article $article, ObjectManager $manager)
    {
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('info', 'L\'article a bien été supprimé');
        return $this->redirectToRoute('blog');
    }
}
