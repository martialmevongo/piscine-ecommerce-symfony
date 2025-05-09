<?php


namespace App\Controller\admin;

use App\Entity\Product;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController {


	#[Route('/admin/create-product', name: 'admin-create-product')]
	public function displayCreateProduct(CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager) {

		if ($request->isMethod('POST')) {

			$title = $request->request->get('title');			
			$description = $request->request->get('description');
			$price = $request->request->get('price');
			$categoryId = $request->request->get('category-id');
			
			if ($request->request->get('is-published') === 'on') {
				$isPublished = true;
			} else {
				$isPublished = false;
			}

			$category = $categoryRepository->find($categoryId);

			try {
				$product = new Product($title, $description, $price, $isPublished, $category);

				$entityManager->persist($product);
				$entityManager->flush();
			} catch (\Exception $exception) {
				$this->addFlash('error', $exception->getMessage());
			}


		}

		$categories = $categoryRepository->findAll();

		return $this->render('admin/product/create-product.html.twig', [
			'categories' => $categories
		]);
	}


	#[Route('/admin/list-products', name: 'admin-list-products')]
	public function displayListProducts(ProductRepository $productRepository) {

		$products = $productRepository->findAll();

		return $this->render('admin/product/list-products.html.twig', [
			'products' => $products
		]);
	}
}