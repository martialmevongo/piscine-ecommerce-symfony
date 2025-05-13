<?php


namespace App\Controller\admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController {

	#[Route(path: '/admin/create-user', name: 'admin-create-user', methods: ['GET', 'POST'])]
	public function displayCreateUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response{

		if ($request->isMethod('POST')) {

			$email = $request->request->get('email');
			$password = $request->request->get(key: 'password');

			$user = new User();

			$passwordHashed = $userPasswordHasher->hashPassword($user, $password);

			// méthode 1
			//$user->setPassword($passwordHashed);
			//$user->setEmail($email);
			// $user->setRoles(['ROLE_ADMIN']);

			// méthode 2 
			$user->createAdmin($email, $passwordHashed);

			try {
				$entityManager->persist($user);
				$entityManager->flush();
				$this->addFlash('success','Admin créé');
				return $this->redirectToRoute('admin-list-admins');

			} catch(Exception $exception) {

				$this->addFlash('error', 'Impossible de créer l\'admin');

				// si l'erreur vient de la clé d'unicité, je créé un message flash ciblé
				if ($exception->getCode() === '1062') {
					$this->addFlash('error',  'Email déjà pris.');
				}
				
			}


		}

		return $this->render('/admin/user/create-user.html.twig');

	}


	#[Route(path: '/admin/list-admins', name: 'admin-list-admins', methods: ['GET'])]
	public function displayListAdmins(UserRepository $userRepository): Response {

		$users = $userRepository->findAll();

		return $this->render('/admin/user/list-users.html.twig', [
			'users' => $users
		]);
	}



}