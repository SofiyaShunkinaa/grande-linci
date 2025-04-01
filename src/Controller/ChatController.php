<?php
// src/Controller/ChatController.php
namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/chat', name: 'chat')]
    public function chat(): Response
    {
        // Получаем текущего пользователя
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Проверяем, есть ли у пользователя уже чат
        $chat = $this->entityManager->getRepository(Chat::class)->findOneBy(['user' => $user]);

        // Если чата нет, создаём новый
        if (!$chat) {
            $admin = $this->entityManager->getRepository(User::class)->findOneBy(['roles' => 'ROLE_ADMIN']);

            if (!$admin) {
                return $this->render('chat/error.html.twig', [
                    'message' => 'Администратор не найден.',
                ]);
            }

            $chat = new Chat();
            $chat->setUser($user);
            $chat->setAdmin($admin);

            $this->entityManager->persist($chat);
            $this->entityManager->flush();
        }

        // Показываем чат
        return $this->redirectToRoute('chat_show', ['chatId' => $chat->getId()]);
    }

    #[Route('/chat/{chatId?}', name: 'chat_show')]
    public function showChat(?int $chatId = null): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Проверяем, есть ли у пользователя уже чат
        $chat = $this->entityManager->getRepository(Chat::class)->findOneBy(['user' => $user]);

        if (!$chat) {
            // Ищем первого администратора
            $admin = $this->entityManager->getRepository(User::class)->findOneBy(['roles' => 'ROLE_ADMIN']);

            if (!$admin) {
                return $this->render('chat/error.html.twig', [
                    'message' => 'Администратор не найден.',
                ]);
            }

            // Создаем новый чат
            $chat = new Chat();
            $chat->setUser($user);
            $chat->setAdmin($admin);

            $this->entityManager->persist($chat);
            $this->entityManager->flush();
        }

        return $this->render('chat/show.html.twig', [
            'chat' => $chat,
        ]);
    }

}
