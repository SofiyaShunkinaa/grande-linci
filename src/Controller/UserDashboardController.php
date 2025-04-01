<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\Chat;
use Doctrine\ORM\EntityManagerInterface;


#[IsGranted('ROLE_USER')]
class UserDashboardController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    public function index(AuthorizationCheckerInterface $authChecker): Response
    {
        $user = $this->getUser();

        // Находим чат этого пользователя
        $chat = $this->entityManager->getRepository(Chat::class)->findOneBy(['user' => $user]);


        // Проверим, что пользователь авторизован и имеет роль ROLE_USER
        if (!$authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login'); // Если не авторизован, перенаправим на страницу входа
        }

        return $this->render('user_dashboard/index.html.twig',[
            'chat' => $chat,
        ]);
    }
}
