<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Reviews;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;


class ProductController extends AbstractController
{
    private $client;
    private $entityManager;

    public function __construct(HttpClientInterface $client,EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }
    



    #[Route('/', name: 'List_Product', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $res = $this->client->request(
            'GET',
            'https://tech.dev.ats-digital.com/api/products?size=50'
        );
        $data = json_decode($res->getContent(),true);

        $entityManager = $this->entityManager;

        foreach ($data['products'] as $prod) {
            $product = new Product();
            $product->setProductName($prod['productName']);
            $product->setDescription($prod['description']);
            $product->setPrice($prod['price']);
            $product->setCategory($prod['category']);
            $product->setImageURL($prod['imageUrl']);
            $entityManager->persist($product);
                //$entityManager->flush();

                
            foreach ($prod['reviews'] as $rev) {
                $review = new Reviews();
                $review->setvalue($rev['value'] ?? null);
                $review->setcontent($rev['content'] ?? null);
                $review->addProduct($product);
                $entityManager->persist($review);
                $entityManager->flush();
                        


                }
                
            $listProd = $productRepository->findAll();
            return $this->render('product/index.html.twig' , ['ListProd' =>$listProd
            ]);

        }
        // dd($data);

        $listProd = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'ListProd' => $listProd
        ]);
    }


    #[Route('/{id}', name: 'show_product', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $reviews = $product->getReviews();

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews
        ]);
    }
}
