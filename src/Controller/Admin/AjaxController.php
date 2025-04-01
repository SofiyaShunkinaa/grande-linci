<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\Kitten;
use App\Repository\UserRepository;
use App\Repository\KittenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends AbstractController
{
    #[Route('/admin/ajax/user-info', name: 'admin_ajax_user_info', methods: ['GET'])]
    public function getUserInfo(Request $request, UserRepository $userRepository): JsonResponse
    {
        $userId = $request->query->get('id');
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'Пользователь не найден'], 404);
        }

        return new JsonResponse([
            'name' => $user->getFirstName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
        ]);
    }

    #[Route('/admin/ajax/kitten-info', name: 'admin_ajax_kitten_info', methods: ['GET'])]
    public function getKittenInfo(Request $request, KittenRepository $kittenRepository): JsonResponse
    {
        $kittenId = $request->query->get('id');
        $kitten = $kittenRepository->find($kittenId);

        if (!$kitten) {
            return new JsonResponse(['error' => 'Котенок не найден'], 404);
        }

        return new JsonResponse([
            'name' => $kitten->getName(),
            'litter' => $kitten->getLitter()->getName(),
            'color' => $kitten->getColor()->getName(),
        ]);
    }
}
