<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleLike;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Form\ContactFormType;
use App\Repository\ArticleLikeRepository;
use Doctrine\ORM\EntityManagerInterface as ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(CacheInterface $cache)
    {
        /*
        $articles = $cache->get('article', function(ItemInterface $item) {
            $item->expiresAfter(10);
            $articleRepo = $this->getDoctrine()->getRepository(Article::class);
            return $articleRepo->findAll();
        });
        */
        $articleRepo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $articleRepo->findAll();
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

    /**
     * @Route("/blog/contact", name="blog_contact")
     */
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = (new \Swift_Message('Test Email'))
                ->setFrom('test@example.com')
                ->setTo('test@example.com')
                ->setBody(
                    $this->renderView(
                        'emails/test.html.twig', [
                            'email' => $data['email'],
                            'content' => $data['content']
                        ]
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
        }
        return $this->render('blog/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}/like", name="article_like")
     */
    public function like(Article $article, ObjectManager $manager, ArticleLikeRepository $likeRepo)
    {
        $user = $this->getUser();
        // Si user n'est pas connecter
        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => 'Vous devez être connecté'
            ], 200);
        }
        //
        if ($article->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'article' => $article,
                'users' => $user
            ]);
            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like bien supprimé !',
                'like' => $likeRepo->count(['article' => $article])
            ], 200);
        }

        $like = new ArticleLike();

        $like
            ->setArticle($article)
            ->setUsers($user)
        ;
        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'like' => $likeRepo->count(['article' => $article])
        ], 200);
    }
}
