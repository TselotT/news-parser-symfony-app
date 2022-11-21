<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;
    private $registry;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
        $this->registry = $registry;
    }
    public function getCommentPaginator(int $offset = 1): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }

    public function parseNewsFromAPI()
    {
        $entityManager = $this->registry->getManager();
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            'https://newsapi.org/v2/everything?q=bitcoin&apiKey=ad948cd68b504314b3fd00b4196d1476'
        );
        $contents = $response->toArray();
        $articles = $contents['articles'];
        $repository = $this->registry->getRepository(News::class);
        foreach ($articles as $article) {
            $news = new News();
            $product = $repository->findOneBy(['title' => $article['title']]);
            if($product){

                // data already exists so checking if the date is recent
                $dateFromApi = date('Y-m-d h:i:s', strtotime($article['publishedAt']));
                $dateFromDb = date('Y-m-d h:i:s', strtotime($product->getPublishedAt()));
                if($dateFromApi > $dateFromDb){
                    // the data is recent so inserting it to db
                    $product->setTitle($article['title']);
                    $article['author'] ? $product->setAuthor($article['author']) : $product->setAuthor("Anonymose");
                    $product->setDescription($article['description']);
                    $product->setUrlToImage($article['urlToImage']);
                    $product->setContent($article['content']);
                    $product->setPublishedAt($article['publishedAt']);
                    $entityManager->persist($product);
                    $entityManager->flush();
                }
                else{
                    // the date is not recent so im skiping it
                    continue;
                }
            }
            else{

                // data being inserted for the first time
                $news->setTitle($article['title']);
                $article['author'] ? $news->setAuthor($article['author']) : $news->setAuthor("Anonymose");
                $news->setDescription($article['description']);
                $news->setUrlToImage($article['urlToImage']);
                $news->setContent($article['content']);
                $news->setPublishedAt($article['publishedAt']);
                $entityManager->persist($news);
                $entityManager->flush();
            }
        }
        error_log("Successfully executed the command from cron job");
    }

    public function save(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return News[] Returns an array of News objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?News
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
