<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class FileService
{

    private  EntityManagerInterface $entityManager;
    private string $publicDir;

    private Filesystem $filesystem;

    public function __construct(EntityManagerInterface $entityManager, string $publicDir)
    {
        $this->entityManager = $entityManager;
        $this->publicDir = $publicDir;
    }

    public function uploadFile(UploadedFile $uploadedFile, string $collection): File|Response
    {
        try {
            $fileName = uniqid('', true) . '.' . $uploadedFile->guessExtension();
            $file = new File();
            $file->setName($fileName)
                ->setType($uploadedFile->guessExtension())
                ->setPath("/$collection/" . $fileName)
                ->setCollection($collection);

            $uploadedFile->move($this->publicDir . "/$collection/", $fileName);
            $this->entityManager->persist($file);
        } catch (FileException $e) {
            return new Response($e->getMessage());
        }

        return $file;
    }

}