<?php

namespace WechatWorkMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/wechat/work')]
class AttachMenuController extends AbstractController
{
    #[Route(path: '/crm1', name: 'wechat-work-attach-menu-crm1')]
    public function crm1(): Response
    {
        return $this->json([
            'time' => time(),
            'method' => __METHOD__,
        ]);
    }

    #[Route(path: '/crm2', name: 'wechat-work-attach-menu-crm2')]
    public function crm2(): Response
    {
        return $this->json([
            'time' => time(),
            'method' => __METHOD__,
        ]);
    }
}
