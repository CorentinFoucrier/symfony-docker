<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $articleRepo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $articleRepo->findAll();

        return $this->render('blog/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    public function show(Article $article)
    {
        return $this->render('blog/show.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/blog/article/edit/{id}", name="blog_edit")
     */
    public function edit(Article $articles)
    {
        return $this->render('blog/edit.html.twig', []);
    }
}
