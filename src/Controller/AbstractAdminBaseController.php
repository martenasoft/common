<?php

namespace MartenaSoft\Common\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Entity\ConfirmDeleteEntity;
use MartenaSoft\Common\Event\CommonConfirmAfterSubmit;
use MartenaSoft\Common\Event\CommonFormEventInterface;
use MartenaSoft\Common\Form\ConfirmDeleteFormType;
use MartenaSoft\Trash\Entity\TrashEntityInterface;
use MartenaSoft\Common\Entity\CommonEntityInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAdminBaseController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->initListener();
    }

    protected function confirmDelete(
        Request $request,
        CommonEntityInterface $entity,
        string $returnUrl,
        array $params = [],
        array $options = []
    ): Response {

        $isTrash =  (
            interface_exists(TrashEntityInterface::class) &&
            $entity instanceof TrashEntityInterface
        );

        $form = $this->createForm(ConfirmDeleteFormType::class, new ConfirmDeleteEntity(), [
            'isShowSafeItem' => $isTrash
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new CommonConfirmAfterSubmitEvent($form);
            if ($event instanceof CommonFormEventInterface) {
                $this->getEventDispatcher()->dispatch($event, CommonFormEventInterface::getEventName());
            }
        }

        return $this->render('@MartenaSoftCommon/common/confirm_delete.html.twig', [
            'form' => $form->createView(),
            'isTrash' => $isTrash,
            'returnUrl' => $returnUrl,
            'params' => $params,
            'options' => $options
        ]);
    }

    protected function initListener(): void
    {

    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
}
