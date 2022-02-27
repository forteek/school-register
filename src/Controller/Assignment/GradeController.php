<?php

namespace App\Controller\Assignment;

use App\Controller\AbstractController;
use App\Entity\Assignment;
use App\Entity\Grade;
use App\Enum\GradeValue;
use App\Enum\UserRole;
use App\Form\GradeAssignmentFormType;
use App\Service\GradesAverageCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GradeController extends AbstractController
{
    #[Route('/assignments/{id}/grades', name: 'index-assignment-grades')]
    #[ParamConverter(data: ['name' => 'assignment'], class: Assignment::class)]
    public function index(
        Assignment $assignment,
        GradesAverageCalculator $averageCalculator
    ): Response {
        $grades = $assignment->getGrades()->toArray();
        $averageGrade = $averageCalculator->calculate($grades);

        return $this->render('assignment/grade/index.html.twig', [
            'assignment_id' => $assignment->getId(),
            'grades' => $grades,
            'grade_count' => count($grades),
            'average_grade' => $averageGrade
        ]);
    }

    #[Route('/assignments/{id}/grades/create', name: 'create-assignment-grade')]
    #[ParamConverter(data: ['name' => 'assignment'], class: Assignment::class)]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        Assignment $assignment
    ): Response {
        if (!$this->getUser()->hasRole(UserRole::TEACHER)) {
            throw new AccessDeniedException('Tylko nauczyciele mogą oceniać zadania.');
        }

        if ($this->getUser()->getId() !== $assignment->getAssigner()->getId()) {
            throw new AccessDeniedException('Tylko zlecający zadanie może je oceniać.');
        }

        $grade = new Grade();
        $form = $this->createForm(
            GradeAssignmentFormType::class,
            $grade,
            ['group_id' => $assignment->getGroup()->getId()]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $grade->setAssignment($assignment);

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('index-assignment-grades', ['id' => $assignment->getId()]);
        }

        return $this->render('assignment/grade/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/assignments/{assignmentId}/grades/{gradeId}/update', name: 'update-assignment-grade')]
    #[ParamConverter(data: ['name' => 'assignment'], class: Assignment::class, options: ['id' => 'assignmentId'])]
    #[ParamConverter(data: ['name' => 'grade'], class: Grade::class, options: ['id' => 'gradeId'])]
    public function update(
        Request $request,
        EntityManagerInterface $entityManager,
        Assignment $assignment,
        Grade $grade
    ): Response {
        if (!$this->getUser()->hasRole(UserRole::TEACHER)) {
            throw new AccessDeniedException('Tylko nauczyciele mogą oceniać zadania.');
        }

        if ($this->getUser()->getId() !== $assignment->getAssigner()->getId()) {
            throw new AccessDeniedException('Tylko zlecający zadanie może je oceniać.');
        }

        if ($grade->getAssignment()->getId() !== $assignment->getId()) {
            throw new AccessDeniedException('Ocena nie należy do tego zadania.');
        }

        $form = $this->createForm(
            GradeAssignmentFormType::class,
            $grade,
            ['group_id' => $assignment->getGroup()->getId()]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('index-assignment-grades', ['id' => $assignment->getId()]);
        }

        return $this->render('assignment/grade/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
