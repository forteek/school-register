<?php

namespace App\Entity;

use App\Enum\GradeValue;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`grade`')]
class Grade extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;

    #[ORM\ManyToOne(targetEntity: Assignment::class)]
    #[ORM\JoinColumn]
    private Assignment $assignment;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn]
    private Student $student;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $value;

    public function getAssignment(): Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(Assignment $assignment): self
    {
        $this->assignment = $assignment;

        return $this;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
