<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile/edit', name: 'app_user_profile')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Получаем текущего пользователя
        $user = $this->getUser();

        // Создаем форму
        $form = $this->createForm(UserProfileType::class, $user);

        // Обработка запроса
        $form->handleRequest($request);

        // Если форма отправлена и валидна, сохраняем изменения
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Сообщение об успешном обновлении
            $this->addFlash('success', 'Ваши данные были успешно обновлены.');

            return $this->redirectToRoute('app_user_dashboard');
        }

        // Рендерим шаблон с формой
        return $this->render('user_profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
