<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRoles;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', statusCode: 423)]
class UserController extends AbstractController
{
    private const LIMIT_PAGINATION = 10;

    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;


    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('/users', name: 'user_list', methods: ['GET', 'HEAD'])]
    public function index(Request $request): Response
    {
        $query = $this->userRepository->findAll();
        $search = $request->get('search');
        $role = $request->get('role');
        $limit = (int) $request->get('limit', self::LIMIT_PAGINATION);

        if ($search) {
            $query = $this->userRepository->findBySearchKeyword($search);
        }

        if ($role) {
            $query = $this->userRepository->findByRole($role);
        }

        return $this->render('user/index.html.twig', [
            'users' => $this->userRepository->findPaginatedUsers($query, $request, $limit),
            'roles' => UserRoles::cases(),
            'roleSelected' => $role,
            'search' => $search,
            'limit' => $limit
        ]);
    }

    #[Route('/users/create', name: 'user_create', methods: ['GET', 'HEAD', 'POST'])]
    public function create(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Your user has been created!'
            );

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'HEAD', 'PATCH'])]
    public function edit(User $user, Request $request): Response
    {
        $form = $this->createForm(UserFormType::class, $user, [
            'method' => 'PATCH',
            'is_password_required' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Your user has been updated!'
            );

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/users/{id}/show', name: 'user_show', methods: ['GET', 'HEAD'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', compact('user'));
    }

    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['GET', 'HEAD'])]
    public function delete(User $user): Response
    {
        $name = $user->getFullName();
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash(
            'success',
            $name .' has been deleted!'
        );

        return $this->redirectToRoute('user_list');
    }
}
