<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\Securizer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository(Task::class)->findAll()]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $task->setUser($this->getUser());
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request, Securizer $securizer)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isUser = $this->checkIfUser($task, $securizer);
            if (!$isUser) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'La tâche a bien été modifiée.');
            }

            return $isUser ?? $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, Securizer $securizer)
    {
        $isUser = $this->checkIfUser($task, $securizer);
        if (!$isUser) {
            $task->toggle(!$task->isDone());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        }
        return $isUser ?? $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task, Securizer $securizer)
    {
        $isUser = $this->checkIfUser($task, $securizer);
        if (!$isUser) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');
        }

        return $isUser ?? $this->redirectToRoute('task_list');
    }

    /**
     * Check if user equal user task
     * @param Task $task
     * @param Securizer $securizer
     * @return RedirectResponse|null
     */
    private function checkIfUser(Task $task, Securizer $securizer)
    {
        if ($this->getUser() !== $task->getUser() && !$securizer->isGranted($this->getUser(), 'ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\' avez pas le droit de modifier cette tâche.');

            return $this->redirectToRoute('task_list');
        }
    }
}
