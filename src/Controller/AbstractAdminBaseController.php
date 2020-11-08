<?php

namespace MartenaSoft\Common\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Entity\BaseEntityInterface;
use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Common\Entity\SafeDeleteEntityInterface;
use MartenaSoft\Common\Form\ConfirmDeleteFormType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAdminBaseController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    protected function confirmDelete(
        Request $request,
        CommonEntityInterface $entity,
        string $returnUrl,
        array $params = []
    ): Response {

        $isShowSafeItem = ($entity instanceof SafeDeleteEntityInterface);
        $form = $this->createForm(ConfirmDeleteFormType::class, null, ['isShowSafeItem' => $isShowSafeItem]);
        return $this->render('@MartenaSoftCommon/common/confirm_delete.html.twig', [
            'form' => $form->createView(),
            'isShowSafeItem' => $isShowSafeItem,
            'returnUrl' => $returnUrl,
            'params' => $params
        ]);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}

