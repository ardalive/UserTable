<?php
declare(strict_types = 1);
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index() :Response
    {
        return $this->redirectToRoute('users');
    }
    /**
     * @Route("/users", name="users")
     */
    public function userTable(UserRepository $userRepository) :Response
    {
        $users = $userRepository->findAll();
        return $this->render('homepage/homepage.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/{action}", name="admin")
     */
    public function admin(UserRepository $userRepository, Request $request, string $action) :Response
    {
        $userIdArray = $request->request->keys();
        foreach ($userIdArray as $userId){
            $user = $userRepository->findOneBy(['id'=>$userId]);
            $entityManager = $this->getDoctrine()->getManager();
            switch ($action){
                case 'block':
                    $user->setIsActive(false);
                    break;
                case 'unblock':
                    $user->setIsActive(true);
                    break;
                case 'delete':
                    $user->setIsDeleted(true);
                    break;
            }
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('users');
    }
}
