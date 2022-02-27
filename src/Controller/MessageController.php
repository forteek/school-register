<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Message;
use App\Entity\Student;
use App\Entity\User;
use App\Enum\UserRole;
use App\Form\CreateMessageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'index-received-messages')]
    public function indexReceived(
        EntityManagerInterface $entityManager
    ): Response {
        $repository = $entityManager->getRepository(Message::class);
        $messages = $repository->findBy(['recipient' => $this->getUser()]);

        return $this->render('message/index-received.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/messages/{id}', name: 'show-message', requirements: ['id' => '\d+'])]
    #[ParamConverter(data: ['name' => 'message'], class: Message::class)]
    public function show(
        EntityManagerInterface $entityManager,
        Message $message
    ): Response {
        if ($this->getUser()->getId() !== $message->getRecipient()->getId()) {
            throw new AccessDeniedException('Tylko odbiorca wiadomości może ją wyświetlać.');
        }

        $message->setRead(true);
        $entityManager->persist($message);
        $entityManager->flush();

        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/messages/sent', name: 'index-sent-messages')]
    public function indexSent(
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->getUser()->hasRole(UserRole::TEACHER)) {
            throw new AccessDeniedException('Tylko nauczyciele mogą wysyłać wiadomości.');
        }

        $repository = $entityManager->getRepository(Message::class);
        $messages = $repository->findBy(['sender' => $this->getUser()]);

        return $this->render('message/index-sent.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/messages/create', name: 'create-message')]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->getUser()->hasRole(UserRole::TEACHER)) {
            throw new AccessDeniedException('Tylko nauczyciele mogą wysyłać wiadomości.');
        }

        $message = new Message();
        $form = $this->createForm(CreateMessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message
                ->setSender($this->getUser())
                ->setRead(false)
            ;
            /** @var ?User $recipientPerson */
            $recipientPerson = $form->get('recipient_person')->getData();
            /** @var ?Group $recipientGroup */
            $recipientGroup = $form->get('recipient_group')->getData();

            if ($recipientPerson && $recipientGroup) {
                $this->addFlash('Wiadomości nie można kierować jednocześnie do pojedynczego rodzica i do klasy.');

                return $this->redirectToRoute('create-message');
            }

            if (!$recipientPerson && !$recipientGroup) {
                $this->addFlash('Wiadomość musi mieć adresata.');

                return $this->redirectToRoute('create-message');
            }

            if ($recipientPerson) {
                $message->setRecipient($recipientPerson);
                $entityManager->persist($message);
            } else {
                $parentsDone = [];
                /** @var Student $student */
                foreach ($recipientGroup->getStudents() as $student) {
                    if (in_array($student->getParent()->getId(), $parentsDone)) {
                        continue;
                    }

                    $parentMessage = clone $message;
                    $parentMessage->setRecipient($student->getParent());
                    $entityManager->persist($parentMessage);

                    $parentsDone[] = $student->getParent()->getId();
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('index-sent-messages');
        }

        return $this->render('message/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
