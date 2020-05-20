<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\CommentType;
use App\Entity\Comment;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        //Plus nécessaire à cause de l'argument dans la fonction index
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();
        
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */

    public function home(){
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue les amis !",
            'age' => 31

        ]);
    }

        /**
          * @Route("/blog/new", name="blog_create")
          * @Route("/blog/{id}/edit", name="blog_edit")
          */

            public function form(Article $article = null, Request $request, ObjectManager $manager) {
                
                if(!$article){
                   $article = new Article(); 
                }
                

                $article->setTitle("Titre d'exemple")
                    ->setContent("Le contenu de l'article");

               // $form = $this->createFormBuilder($article)
               //                 ->add('title') 
               //                 ->add('content')  
               //                 ->add('image')               
               //                 ->getForm();
                
                $form = $this->createForm(ArticleType::class, $article);

                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()) {
                    if(!$article->getId()){
                         $article->setCreatedAt(new \Datetime());
                    }
                   

                    $manager->persist($article);
                    $manager->flush();

                    return $this->redirectToRoute('blog_show', ['id' => $article->getId
                    ()]);
                }

                return $this->render('blog/create.html.twig', [
                    'formArticle' => $form->createView(),
                    'editMode' => $article->getId() !==null
                ]);
            }
    

     /**
     * @Route("/blog/{id}", name="blog_show")
     */

    public function show(Article $article, Request $request, ObjectManager $manager){
    //public function show(ArticleRepository $repo, $id){
        
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        //$articles = $repo->find($id);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            
            $manager->persist($comment);
            $manager->flush();
        }
        
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);

        }

    }

        