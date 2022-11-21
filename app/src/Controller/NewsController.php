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
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ParseNewsFromApi;

class NewsController extends AbstractController
{
    #[Route('/', name: 'app_news')]
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request, MessageBusInterface $messageBus): Response
    {
        // $message = new ParseNewsFromApi();
        // $messageBus->dispatch($message);
        $repository = $doctrine->getRepository(News::class);

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
