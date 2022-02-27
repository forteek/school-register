<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`student`')]
class Student extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $firstName;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $lastName;

    #[ORM\Column(type: Types::STRING, length: 511, nullable: true)]
    private ?string $photoUrl;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn]
    private User $parent;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn]
    private Group $group;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Grade::class)]
    private Collection $grades;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $url): self
    {
        $this->photoUrl = $url;

        return $this;
    }

    public function getParent(): User
    {
        return $this->parent;
    }

    public function setParent(User $user): self
    {
        $this->parent = $user;

        return $this;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function setGrades(Collection $grades): self
    {
        $this->grades = $grades;

        return $this;
    }
}
