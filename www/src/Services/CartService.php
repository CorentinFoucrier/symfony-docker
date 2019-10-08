<?php
namespace App\Services;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    protected $session;

    protected $articleRepository;

    public function __construct(
        SessionInterface $session,
        ArticleRepository $articleRepository
    )
    {
        $this->session = $session;
        $this->articleRepository = $articleRepository;
    }
    
    public function add($id)
    {
        $panier = $this->session->get("panier", []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
    }

    public function delete($id)
    {
        $panier = $this->session->get("panier", []);

        if ($panier[$id]) {
            unset($panier[$id]);
        }
        $this->session->set("panier", $panier);
    }

    public function getAll()
    {
        $panier = $this->session->get('panier', []);
        $cartWithData = [];
        foreach ($panier as $id => $qty) {
            $cartWithData[] = [
                'article' => $this->articleRepository->find($id),
                'qty' => $qty
            ];
        }

        return $cartWithData;
    }

    public function getTotal($cartWithData)
    {
        $total = 0;
        foreach ($cartWithData as $value) {
            $total += $value['article']->getPrice() * $value['qty'];
        }
        return $total;
    }
}
