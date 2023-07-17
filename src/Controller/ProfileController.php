<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use App\Form\ProfileFormType;

use App\Repository\UserRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    private const COLLECTION_IMAGES = 'images';

    public function __construct(
        private readonly FileService  $fileService,
        private UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager
    ){}

    #[Route('/profile', name: 'profile', methods: ['GET','HEAD', 'PATCH'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $this->getUser(), ['method' => 'PATCH']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $form->get('file')->getData();

            if ($avatar) {
                $user->getFile() !== null ? $this->userRepository->removeImage($user) : '';
                $file = $this->fileService->uploadFile($avatar, self::COLLECTION_IMAGES);
                $user->setFile($file);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Your data has been updated!'
            );

            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/{id}/remove-image', name: 'profile_remove_image', methods: ['GET','HEAD'])]
    public function removeImage(User $user): RedirectResponse
    {
        $this->userRepository->removeImage($user);
        $this->addFlash(
            'success',
            'Your image has been deleted'
        );

        return $this->redirectToRoute('profile');
    }

}
