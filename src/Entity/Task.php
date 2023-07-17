<?php

namespace App\Entity;

use App\Enum\Priority;
use App\Enum\Status;
use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:2,
        max:255
    )]
    private ?string $title = null;

    #[ORM\Column(length: 1000)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:2,
        max:1000
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', enumType: Status::class)]
    #[Assert\NotBlank]
    #[Assert\Type(type: Status::class)]
    private Status|null $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type(\DateTime::class)]
    private ?DateTimeInterface $deadline = null;

    #[ORM\Column(type: 'integer', enumType: Priority::class)]
    #[Assert\NotBlank]
    #[Assert\Type(type: Priority::class)]
    private Priority|null $priority = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDeadline(): ?DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    public function setPriority(Priority $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getFormattedDeadline(): ?string
    {
        return $this->getDeadline()?->format('Y-m-d H:i:s');
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
