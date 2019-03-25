<?php

namespace App\Repository;

use App\Entity\Posts;
use App\Entity\Tags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    /**
     * @param int $page
     * @param int $max
     * @param null $tag
     * @return Paginator
     */
    public function findByPage($page = 1, $max = 2, $tag = null)
    {
        $dql = $this->createQueryBuilder('posts');
        if (!$tag) {
            $dql->orderBy('posts.date', 'DESC')->where("posts.visible != false");
        } else {
            $matchedTag = $this->getEntityManager()->getRepository(Tags::class)->findOneBy(["title" => $tag]);
            $dql->innerJoin("posts.tags", "tags")
                ->orderBy('posts.date', 'DESC')
                ->where("posts.visible != false")
                ->andWhere("tags.id = '" . $matchedTag->getId() . "'");
        }
        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    /**
     * @param string $url
     * @return NotFoundHttpException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findByUrl(string $url)
    {
        $em = $this->getEntityManager();
        $dql = $em->createQuery("SELECT p FROM App\Entity\Posts p WHERE p.visible != :visible AND p.url = :url");
        $dql->setParameters([
            "visible" => false,
            "url" => $url
        ]);
        $result = $dql->getResult();
        if (!$result) {
            return new NotFoundHttpException('Page not found');
        }
        $this->increaseViews($url);

        return $result[0];
    }

    /**
     * @param string $url
     * @return NotFoundHttpException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function increaseViews(string $url)
    {
        $em = $this->getEntityManager();
        $post = $em->getRepository(Posts::class)->findOneBy(["url" => $url]);
        if (!$post) {
            return new NotFoundHttpException("Page not found");
        }
        $post->setViews($post->getViews() + 1);
        $em->flush();
    }


    /**
     * @param array $posts
     * @return array
     */
    public function PostsFilteredArray(array $posts): array
    {
        $postsArray = [];
        foreach ($posts as $post) {
            $postsArray[] = [
                "id" => $post->getId(),
                "title" => $post->getTitle(),
                "date" => $post->getDate(),
                "url" => $post->getUrl(),
                "views" => $post->getViews(),
                "visible" => $post->getVisible()
            ];
        }
        return $postsArray;
    }


    /**
     * @param $array
     * @return Response
     */
    public function returnAsJSON($array): Response
    {
        $response = new Response();
        $response->setContent(json_encode((array)$array));
        $response->headers->set("Content-Type", "application/json");

        return $response;
    }

    // /**
    //  * @return Posts[] Returns an array of Posts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Posts
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
