<?php

namespace App\Controller\Admin;

use App\Entity\GuestRequest;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;


class GuestRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GuestRequest::class;
    }
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function configureActions(Actions $actions): Actions
    {
//        $markAsViewed = Action::new('markAsViewed', 'Пометить как прочитанное')
//            ->linkToCrudAction('markAsViewed')
//            ->addCssClass('btn btn-success')
//            ->setIcon('fa fa-eye')
//            ->addCssClass('action-mark-as-viewed')
//            ->createAsBatchAction();

        return $actions
            ->disable(Action::NEW, Action::EDIT)
//            ->add(Crud::PAGE_INDEX, $markAsViewed)
            ->add(Action::INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX) // Добавляем кнопку "К списку" на страницу редактирования
            ->update(Crud::PAGE_EDIT, Action::INDEX, function (Action $action) {
                return $action->setLabel('Назад к записям')
                    ->setIcon('fa fa-arrow-left');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_DETAIL) {
            $request = $this->getContext()->getRequest();
            $id = $request->get('entityId');

            $booking = $this->entityManager->getRepository(GuestRequest::class)->find($id);
            if ($booking && !$booking->isViewedByAdmin()) {
                $booking->setIsViewedByAdmin(true);
                $this->entityManager->flush();
            }
        }
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('email'),
            TextField::new('phone'),
            TextEditorField::new('message'), // слишком длинное — только в detail
            DateField::new('requestDate'),
        ];
    }

//    public function markAsViewed(AdminContext $context, EntityManagerInterface $entityManager): Response
//    {
//        $selectedIds = $context->getRequest()->get('entityIds', []);
//
//        if (!is_array($selectedIds)) {
//            $selectedIds = [];
//        }
//
//        $repository = $entityManager->getRepository(GuestRequest::class);
//        $guestRequests = $repository->findBy(['id' => $selectedIds]);
//
//        foreach ($guestRequests as $request) {
//            if (method_exists($request, 'setIsViewedByAdmin')) {
//                $request->setIsViewedByAdmin(true);
//                $entityManager->persist($request);
//            }
//        }
//        $entityManager->flush();
//
//        $this->addFlash('success', 'Выбранные заявки были помечены как прочитанные.');
//
//        return $this->redirect($context->getReferrer() ?? $this->generateUrl('admin'));
//    }

}
