<?php

namespace App\Repository;

use App\Controller\TaskController;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Task::class);
    }


    public function findPaginatedTasks($query, Request $request, $limit): PaginationInterface
    {
        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );
    }

    public function findBySearchKeyword(string $search): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.title LIKE :title')
            ->setParameter('title', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    public function findTasksByUser(User $user): array
    {
        return $user->isAdmin() ? $this->findAll() : $this->findBy(['user' => $user->getId()]);
    }

    public function findFilterTasksByRequestParams(Request $request, User $user): array
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $priority =  $request->get('priority');

        if ($search) {
            return $this->findBySearchKeyword($search);
        }

        if ($priority && !$status) {
            return $this->findBy(['priority' => $priority]);
        }

        if ($status && !$priority) {
            return $this->findBy(['status' => $status]);
        }

        if ($status && $priority) {
            return $this->findBy(['status' => $status, 'priority' => $priority]);
        }

        return $this->findTasksByUser($user);
    }


//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
