<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkMenuBundle\Enum\MenuButtonType;
use Tourze\WechatWorkMenuBundle\Exception\MenuButtonException;
use Tourze\WechatWorkMenuBundle\Repository\MenuButtonRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/90231
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: MenuButtonRepository::class)]
#[ORM\Table(name: 'wechat_work_menu_button', options: ['comment' => '菜单按钮'])]
class MenuButton implements ApiArrayInterface, \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?AgentInterface $agent = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '标签名'])]
    private string $name;

    #[Assert\Choice(callback: [MenuButtonType::class, 'cases'])]
    #[ORM\Column(length: 40, nullable: true, enumType: MenuButtonType::class, options: ['comment' => '按钮类型'])]
    private ?MenuButtonType $type = null;

    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '点击键值'])]
    private ?string $clickKey = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '网页链接'])]
    private ?string $viewUrl = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '小程序AppId'])]
    private ?string $miniProgramAppId = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '小程序路径'])]
    private ?string $miniProgramPath = null;

    #[ORM\ManyToOne(targetEntity: MenuButton::class, cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?MenuButton $parent = null;

    /**
     * @var Collection<int, MenuButton>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: MenuButton::class)]
    private Collection $children;

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[Groups(groups: ['admin_curd', 'api_tree', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): void
    {
        $this->agent = $agent;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function getType(): ?MenuButtonType
    {
        return $this->type;
    }

    public function setType(?MenuButtonType $type): void
    {
        $this->type = $type;
    }

    public function getClickKey(): ?string
    {
        return $this->clickKey;
    }

    public function setClickKey(?string $clickKey): void
    {
        $this->clickKey = $clickKey;
    }

    public function getViewUrl(): ?string
    {
        return $this->viewUrl;
    }

    public function setViewUrl(?string $viewUrl): void
    {
        $this->viewUrl = $viewUrl;
    }

    public function getMiniProgramAppId(): ?string
    {
        return $this->miniProgramAppId;
    }

    public function setMiniProgramAppId(?string $miniProgramAppId): void
    {
        $this->miniProgramAppId = $miniProgramAppId;
    }

    public function getMiniProgramPath(): ?string
    {
        return $this->miniProgramPath;
    }

    public function setMiniProgramPath(?string $miniProgramPath): void
    {
        $this->miniProgramPath = $miniProgramPath;
    }

    public function getParent(): ?MenuButton
    {
        return $this->parent;
    }

    public function setParent(?MenuButton $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection<int, MenuButton>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(MenuButton $child): void
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
    }

    public function removeChild(MenuButton $child): void
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        if ($this->getChildren()->count() > 0) {
            $result = [
                'name' => $this->getName(),
                'sub_button' => [],
            ];
            foreach ($this->getChildren() as $child) {
                $result['sub_button'][] = $child->retrieveApiArray();
            }

            return $result;
        }

        if (null === $this->getType()) {
            throw new MenuButtonException('按钮类型不能为空');
        }

        $result = [
            'type' => $this->getType()->value,
            'name' => $this->getName(),
        ];
        if (null !== $this->getClickKey()) {
            $result['key'] = $this->getClickKey();
        }
        if (MenuButtonType::View === $this->getType()) {
            $result['url'] = $this->getViewUrl();
        }
        if (MenuButtonType::ViewMiniProgram === $this->getType()) {
            $result['appid'] = $this->getMiniProgramAppId();
            $result['pagepath'] = $this->getMiniProgramPath();
        }

        return $result;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
