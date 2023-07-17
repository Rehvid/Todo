<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\Priority;
use App\Enum\Status;
use App\Form\TaskFormType;
use App\Form\TaskStatusFormType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER', statusCode: 423)]
class TaskController extends AbstractController
{
    public const LIMIT_PAGINATION = 10;
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/tasks', name: 'task_list', methods: ['GET', 'HEAD'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $limit = (int) $request->get('limit', self::LIMIT_PAGINATION);
        $search = $request->get('search');
        $status = (int) $request->get('status');
        $priority = (int) $request->get('priority');

        $tasks = $this->taskRepository->findPaginatedTasks(
            $this->taskRepository->findFilterTasksByRequestParams($request, $this->getUser()),
            $request,
            $limit
        );

        return $this->render('task/index.html.twig', [
            'search' => $search,
            'statuses' => Status::cases(),
            'selectedStatus' => $status,
            'priorities' => Priority::cases(),
            'selectedPriority' => $priority,
            'limit' => $limit,
            'tasks' => $tasks
        ]);
    }

    #[IsGranted('ROLE_ADMIN', statusCode: 423)]
    #[Route('/tasks/create/', name: 'task_create', methods: ['GET', 'HEAD', 'POST'])]
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task, [
            'users' => $this->entityManager->getRepository(User::class)
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                $task->getTitle() .' has been created!'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tasks/{id}/show', name: 'task_show', methods: ['GET', 'HEAD', 'PATCH'])]
    public function show(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskStatusFormType::class, $task, [
            'method' => 'PATCH'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Your status has been updated'
            );

            return $this->redirectToRoute('task_show', ['id' => $task->getId()]);
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_ADMIN', statusCode: 423)]
    #[Route('/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'HEAD', 'PATCH'])]
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskFormType::class, $task, [
            'users' => $this->entityManager->getRepository(User::class),
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                  $task->getTitle() .' has been updated!'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[IsGranted('ROLE_ADMIN', statusCode: 423)]
    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: ['GET', 'DELETE'])]
    public function delete(Task $task): Response
    {
        $this->addFlash(
            'success',
             $task->getTitle() .' has been deleted!'
        );

        $this->entityManager->remove($task);
        $this->entityManager->flush();
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/done', name: 'task_done', methods: ['GET', 'HEAD', 'POST'])]
    public function done(Task $task): Response
    {
        $this->addFlash(
            'success',
            $task->getTitle() .' has been successfully done!'
        );

        $task->setStatus(Status::DONE);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('task_list');
    }

}
