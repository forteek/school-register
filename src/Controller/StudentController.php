<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\CreateStudentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class StudentController extends AbstractController
{
    #[Route('/students', name: 'index-students')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response {
        $repository = $entityManager->getRepository(Student::class);
        $students = $repository->findBy(['parent' => $this->getUser()]);

        return $this->render('student/index.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/students/create', name: 'create-student')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $student = new Student();
        $form = $this->createForm(CreateStudentFormType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Podczas dodawania zdjęcia wystąpił bład.');
                }

                $student->setPhotoUrl($newFilename);
            }

            $student->setParent($this->getUser());

            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('index-students');
        }

        return $this->render('student/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/students/{id}/destroy', name: 'destroy-student', requirements: ['id' => '\d+'])]
    #[ParamConverter(data: ['name' => 'student'], class: Student::class)]
    public function destroy(Student $student, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($student);
        $entityManager->flush();

        $this->addFlash('red', 'Zniszczono dziecko.');

        return $this->redirectToRoute('index-students');
    }
}
