<?php


namespace App\Controller\guest;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController {


	#[Route('/list-products', name:'list-products', methods: ['GET'])]
	public function displayListProducts(ProductRepository $productRepository): Response {
		
		$productsPublished = $productRepository->findBy(['isPublished' => true]);

		return $this->render('guest/product/list-products.html.twig', [
			'products' => $productsPublished
		]);
	}

	#[Route('/details-product/{id}', name:'details-product', methods: ['GET'])]
	public function displayDetailsProduct(ProductRepository $productRepository, int $id): Response {
		
		$product = $productRepository->find($id);

		if(!$product) {
			return $this->redirectToRoute("404");
		}

		return $this->render('guest/product/details-product.html.twig', [
			'product' => $product
		]);

	}

}