<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Enum\UserRole;
use App\Form\CreateAssignmentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AssignmentController extends AbstractController
{
    #[Route('/assignments', name: 'index-assignments')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response {
        $repository = $entityManager->getRepository(Assignment::class);
        $assignments = $repository->findBy(['assigner' => $this->getUser()]);

        return $this->render('assignment/index.html.twig', [
            'assignments' => $assignments,
        ]);
    }

    #[Route('/assignments/create', name: 'create-assignment')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->getUser()->hasRole(UserRole::TEACHER)) {
            throw new AccessDeniedException('Tylko nauczyciele mogą dodawać zadania.');
        }

        $assignment = new Assignment();
        $form = $this->createForm(CreateAssignmentFormType::class, $assignment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assignment->setAssigner($this->getUser());

            $entityManager->persist($assignment);
            $entityManager->flush();

            return $this->redirectToRoute('index-assignments');
        }

        return $this->render('assignment/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
