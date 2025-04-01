<?php
// src/Controller/MessageController.php
namespace App\Controller;

use App\Entity\Message;
use App\Entity\Chat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/chat/{chatId}/message', name: 'send_message')]
    public function sendMessage(Request $request, int $chatId): Response
    {
        $chat = $this->entityManager->getRepository(Chat::class)->find($chatId);

        if (!$chat) {
            throw $this->createNotFoundException('Chat not found');
        }

        $message = new Message();
        $message->setChat($chat);

        // Обработка текстового сообщения
        if ($request->get('content')) {
            $message->setContent($request->get('content'));
            $message->setType('text');
        }

        // Обработка графического сообщения (файл)
        if ($request->files->get('image')) {
            /** @var UploadedFile $image */
            $image = $request->files->get('image');
            $imagePath = $this->uploadImage($image);
            $message->setImagePath($imagePath);
            $message->setType('image');
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->redirectToRoute('chat_show', ['chatId' => $chatId]);
    }

    private function uploadImage(UploadedFile $image): string
    {
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images/';
        $fileName = uniqid() . '.' . $image->guessExtension();

        try {
            $image->move($uploadDir, $fileName);
        } catch (FileException $e) {
            throw new \Exception('Error uploading image');
        }

        return '/uploads/images/' . $fileName;
    }
}
