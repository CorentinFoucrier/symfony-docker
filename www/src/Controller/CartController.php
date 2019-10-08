<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartService $cartService)
    {
        $cartWithData = $cartService->getAll();
        $total = $cartService->getTotal($cartWithData);
        
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, CartService $cartServices)
    {
        $cartServices->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/panier/delete/{id}", name="cart_delete")
     */
    public function delete($id, CartService $cartService)
    {
        $cartService->delete($id);

        return $this->redirectToRoute('cart');
    }
}
