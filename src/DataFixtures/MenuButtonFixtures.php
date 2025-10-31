<?php

namespace Tourze\WechatWorkMenuBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\WechatWorkMenuBundle\Entity\MenuButton;
use Tourze\WechatWorkMenuBundle\Enum\MenuButtonType;
use WechatWorkBundle\DataFixtures\AgentFixtures;
use WechatWorkBundle\DataFixtures\CorpFixtures;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

#[When(env: 'test')]
#[When(env: 'dev')]
class MenuButtonFixtures extends Fixture implements DependentFixtureInterface
{
    public const MENU_BUTTON_REFERENCE_PREFIX = 'menu_button_';

    public function load(ObjectManager $manager): void
    {
        $corp = $this->getReference(CorpFixtures::CORP_1_REFERENCE, Corp::class);
        assert($corp instanceof Corp);

        $agent = $this->getReference(AgentFixtures::AGENT_1_REFERENCE, Agent::class);
        assert($agent instanceof Agent);

        $this->createBasicMenuButtons($manager, $corp, $agent);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CorpFixtures::class,
            AgentFixtures::class,
        ];
    }

    private function createBasicMenuButtons(ObjectManager $manager, Corp $corp, Agent $agent): void
    {
        $basicButtons = [
            ['name' => '首页', 'type' => MenuButtonType::Click, 'clickKey' => 'HOME'],
            ['name' => '产品', 'type' => MenuButtonType::View, 'viewUrl' => 'https://www.tourze.com/products'],
            ['name' => '联系我们', 'type' => MenuButtonType::Click, 'clickKey' => 'CONTACT'],
        ];

        foreach ($basicButtons as $index => $buttonData) {
            $button = new MenuButton();
            $button->setName($buttonData['name']);
            $button->setType($buttonData['type']);
            $button->setValid(true);
            $button->setSortNumber($index);
            $button->setCorp($corp);
            $button->setAgent($agent);

            if (isset($buttonData['clickKey'])) {
                $button->setClickKey($buttonData['clickKey']);
            }
            if (isset($buttonData['viewUrl'])) {
                $button->setViewUrl($buttonData['viewUrl']);
            }

            $manager->persist($button);
            $this->addReference(self::MENU_BUTTON_REFERENCE_PREFIX . $index, $button);
        }
    }
}
