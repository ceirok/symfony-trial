<?php
/**
 * Created by PhpStorm.
 * User: ceirokilp
 * Date: 27/09/2018
 * Time: 12:12
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Article;
use App\Form\ArticlesType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ArticlesController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var UploadedFile
     */
    private $file;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        ArticleRepository $articleRepository
    )
    {

        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->articleRepository = $articleRepository;
    }

    public function index(UserRepository $userRepository)
    {
        $articles = $this->articleRepository->findAll();
        return $this->render('articles/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request)
    {
        $user = $this->getUser();

        $article = new Article();
        $article->setUser($user);

        $form = $this->formFactory->create(ArticlesType::class, $article);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $file = $article->getImage();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );

            $article->setImage($fileName);
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('articles_index'));
        }

        return $this->render('articles/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}