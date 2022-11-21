<?php

namespace App\Controller;

use App\Entity\News;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

class NewsController extends AbstractController
{
    #[Route('/', name: 'app_news')]
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        // $entityManager = $doctrine->getManager();
        // $client = HttpClient::create();
        // $response = $client->request(
        //     'GET',
        //     'https://newsapi.org/v2/everything?q=bitcoin&apiKey=ad948cd68b504314b3fd00b4196d1476'
        // );
        // $contents = $response->toArray();
        // $articles = $contents['articles'];
        $repository = $doctrine->getRepository(News::class);
        // foreach ($articles as $article) {
        //     $news = new News();
        //     $product = $repository->findOneBy(['title' => $article['title']]);
        //     if($product){

        //         // data already exists so checking if the date is recent
        //         $dateFromApi = date('Y-m-d h:i:s', strtotime($article['publishedAt']));
        //         $dateFromDb = date('Y-m-d h:i:s', strtotime($product->getPublishedAt()));
        //         if($dateFromApi > $dateFromDb){
        //             // the data is recent so inserting it to db
        //             $product->setTitle($article['title']);
        //             $article['author'] ? $product->setAuthor($article['author']) : $product->setAuthor("No author is given");
        //             $product->setDescription($article['description']);
        //             $product->setUrlToImage($article['urlToImage']);
        //             $product->setContent($article['content']);
        //             $product->setPublishedAt($article['publishedAt']);
        //             $entityManager->persist($product);
        //             $entityManager->flush();
        //         }
        //         else{
        //             // the date is not recent so im skiping it
        //             continue;
        //         }
        //     }
        //     else{

        //         // data being inserted for the first time
        //         $news->setTitle($article['title']);
        //         $article['author'] ? $news->setAuthor($article['author']) : $news->setAuthor("No author is given");
        //         $news->setDescription($article['description']);
        //         $news->setUrlToImage($article['urlToImage']);
        //         $news->setContent($article['content']);
        //         $news->setPublishedAt($article['publishedAt']);
        //         $entityManager->persist($news);
        //         $entityManager->flush();
        //     }
        // }
        $news = $repository->findAll();
        $articles = $paginator->paginate(
            // Doctrine Query, not results
            $news,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );
        // dd($news);
        // return $content;
        return $this->render('index.html.twig',[
            'articles' => $articles
        ]);
    }
    #[Route('/news/delete/{id}', methods:['GET', 'DELETE'], name: 'app_news_delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $repository = $doctrine->getRepository(News::class);
        $news = $repository->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($news);
        $entityManager->flush();
        return $this->redirectToRoute('app_news');
    }

}
