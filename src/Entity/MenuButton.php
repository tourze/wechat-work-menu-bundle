<?php

namespace Tourze\WechatWorkMenuBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Serializer\Attribute\Groups;
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
 */
#[ORM\Entity(repositoryClass: MenuButtonRepository::class)]
#[ORM\Table(name: 'wechat_work_menu_button', options: ['comment' => '菜单按钮'])]
class MenuButton implements ApiArrayInterface, Stringable
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne]
    private ?AgentInterface $agent = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '标签名'])]
    private string $name;

    #[ORM\Column(length: 40, nullable: true, enumType: MenuButtonType::class, options: ['comment' => '按钮类型'])]
    private ?MenuButtonType $type = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '点击键值'])]
    private ?string $clickKey = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '网页链接'])]
    private ?string $viewUrl = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '小程序AppId'])]
    private ?string $miniProgramAppId = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '小程序路径'])]
    private ?string $miniProgramPath = null;

    #[ORM\ManyToOne(targetEntity: MenuButton::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?MenuButton $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: MenuButton::class)]
    private Collection $children;

    #[IndexColumn]
    #[Groups(groups: ['admin_curd', 'api_tree', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): static
    {
        $this->corp = $corp;

        return $this;
    }

    public function getType(): ?MenuButtonType
    {
        return $this->type;
    }

    public function setType(?MenuButtonType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getClickKey(): ?string
    {
        return $this->clickKey;
    }

    public function setClickKey(?string $clickKey): static
    {
        $this->clickKey = $clickKey;

        return $this;
    }

    public function getViewUrl(): ?string
    {
        return $this->viewUrl;
    }

    public function setViewUrl(?string $viewUrl): static
    {
        $this->viewUrl = $viewUrl;

        return $this;
    }

    public function getMiniProgramAppId(): ?string
    {
        return $this->miniProgramAppId;
    }

    public function setMiniProgramAppId(?string $miniProgramAppId): static
    {
        $this->miniProgramAppId = $miniProgramAppId;

        return $this;
    }

    public function getMiniProgramPath(): ?string
    {
        return $this->miniProgramPath;
    }

    public function setMiniProgramPath(?string $miniProgramPath): static
    {
        $this->miniProgramPath = $miniProgramPath;

        return $this;
    }

    public function getParent(): ?MenuButton
    {
        return $this->parent;
    }

    public function setParent(?MenuButton $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, MenuButton>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(MenuButton $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(MenuButton $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }


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
