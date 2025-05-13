<?php


namespace App\Controller\admin;

use App\Entity\Product;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController {


	#[Route('/admin/create-product', name: 'admin-create-product')]
	public function displayCreateProduct(CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager): Response {

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

				$this->addFlash('success', 'Produit créé');

				return $this->redirectToRoute('admin-list-products');
			} catch (Exception $exception) {
				$this->addFlash('error', $exception->getMessage());
			}


		}

		$categories = $categoryRepository->findAll();

		return $this->render('admin/product/create-product.html.twig', [
			'categories' => $categories
		]);
	}


	#[Route('/admin/list-products', name: 'admin-list-products')]
	public function displayListProducts(ProductRepository $productRepository): Response {

		$products = $productRepository->findAll();

		return $this->render('admin/product/list-products.html.twig', [
			'products' => $products
		]);
	}


	#[Route('/admin/delete-product/{id}', name:'admin-delete-product')]
	public function deleteProduct(int $id, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response {
		
		$product = $productRepository->find($id);

		if(!$product) {
			return $this->redirectToRoute('admin_404');
		}

		try {
			$entityManager->remove($product);
			$entityManager->flush();

			$this->addFlash('success', 'Produit supprimé !');

		} catch(Exception $exception) {
			$this->addFlash('error', 'Impossible de supprimer le produit');
		}

		return $this->redirectToRoute('admin-list-products');
	}

	#[Route('/admin/update-product/{id}', name: 'admin-update-product')]
	public function displayUpdateProduct(int $id, ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager): Response {

		$product = $productRepository->find($id);

		if(!$product) {
			return $this->redirectToRoute('admin_404');
		}

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

			// méthode 1 : modifier les données d'un produit avec les fonctions setters
			//$product->setTitle($title);
			//$product->setDescription($description);
			//$product->setPrice($price);
			//$product->setIsPublished($isPublished);
			//$product->setcategory($category);
			//$product->setUpdatedAt(new \DateTime())

			// méthode 2 : modifier les données d'un produit avec une fonction update dans l'entité

			try {
				$product->update($title, $description, $price, $isPublished, $category);	

				$entityManager->persist($product);
				$entityManager->flush();
			} catch (Exception $exception) {
				$this->addFlash('error', $exception->getMessage());
			}

		}

		$categories = $categoryRepository->findAll();

		return $this->render('admin/product/update-product.html.twig', [
			'categories' => $categories,
			'product' => $product
		]);
	}


	/** AUTRE FACON DE GERER LES FORMS AVEC SYMFONY
	 * #[Route('/admin/create-product-form-sf', name: 'admin-create-product-form-sf')]
	*public function displayCreateProductFormSf(Request $request, EntityManagerInterface $entityManager) {
	*
	*	$product = new Product();
	*
	*		$productForm = $this->createForm(ProductForm::class, $product);
	*	$productForm->handleRequest($request);
	*
	*	if ($productForm->isSubmitted()) {
	*		$product->setCreatedAt(new \DateTime());
	*		$product->setUpdatedAt(new \DateTime());
	*
	*		$entityManager->persist($product);
	*		$entityManager->flush();
	*	}
	*	
	*	return $this->render('admin/product/create-product-form-sf.html.twig', [
	*		'productForm' => $productForm->createView()
	*	]);
	*}
	**/

}