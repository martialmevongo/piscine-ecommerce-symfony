<?php


namespace App\Controller\guest;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController {


	#[Route('/list-categories', name:'list-categories', methods: ['GET'])]
	public function displayListCategories(CategoryRepository $categoryRepository): Response {
		
		$categories = $categoryRepository->findAll();

		return $this->render('guest/category/list-categories.html.twig', [
			'categories' => $categories
		]);
	}

	#[Route('/details-category/{id}', name:'details-category', methods: ['GET'])]
	public function displayDetailsCategory(CategoryRepository $categoryRepository, int $id): Response {
		
		$category = $categoryRepository->find($id);

		if(!$category) {
			return $this->redirectToRoute("404");
		}

		return $this->render('guest/category/details-category.html.twig', [
			'category' => $category
		]);

	}

}