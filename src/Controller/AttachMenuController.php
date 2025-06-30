<?php

namespace Tourze\WechatWorkMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @phpstan-ignore-next-line
 */
class AttachMenuController extends AbstractController
{
    /**
     * @phpstan-ignore symfony.requireInvokableController
     */
    #[Route(path: '/wechat/work/crm1', name: 'wechat-work-attach-menu-crm1')]
    public function crm1(): Response
    {
        return $this->json([
            'time' => time(),
            'method' => __METHOD__,
        ]);
    }

    /**
     * @phpstan-ignore symfony.requireInvokableController
     */
    #[Route(path: '/wechat/work/crm2', name: 'wechat-work-attach-menu-crm2')]
    public function crm2(): Response
    {
        return $this->json([
            'time' => time(),
            'method' => __METHOD__,
        ]);
    }
}
