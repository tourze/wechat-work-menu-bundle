<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AttachMenuController extends AbstractController
{
    #[Route(path: '/wechat-work/menu/attach', name: 'wechat_work_menu_attach', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->json(['message' => 'Wechat Work Menu Attach Controller']);
    }
}
