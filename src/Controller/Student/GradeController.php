<?php

namespace App\Controller\Student;

use App\Controller\AbstractController;
use App\Entity\Assignment;
use App\Entity\Student;
use App\Service\GradesAverageCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GradeController extends AbstractController
{
    #[Route('/students/{id}/grades', name: 'index-student-grades')]
    #[ParamConverter(data: ['name' => 'student'], class: Student::class)]
    public function index(
        Student $student,
        GradesAverageCalculator $averageCalculator
    ): Response {
        $grades = $student->getGrades()->toArray();
        $averageGrade = $averageCalculator->calculate($grades);

        return $this->render('student/grade/index.html.twig', [
            'grades' => $grades,
            'grade_count' => count($grades),
            'average_grade' => $averageGrade
        ]);
    }
}
