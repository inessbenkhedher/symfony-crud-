<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Projet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class ProjetController extends AbstractController
{
    #[Route('/projet', name: 'app_projet')]
    public function index(Request $request)
    {
        $form = $this ->createFormBuilder()
        ->add("critere",TextType::class)
        ->add("Valider",SubmitType::class)
        ->getForm();
        $form ->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $repo=$em->getRepository(Projet::class);
        $lesProjets = $repo->findAll();

        if ($form->isSubmitted()){
            $data = $form ->getData();
            $lesProjets = $repo ->recherche($data['critere']);
        }
        

        return $this->render('projet/index.html.twig', [
            'controller_name' => 'ProjetController','lesProjets'=>$lesProjets,'form'=>$form->createView()
        ]);
    }


    #[Route('/Addpro', name:'ajout_pro')]
    public function ajouter2(Request $request){
        $projet = new Projet();
        $fb = $this -> createFormBuilder($projet)
        ->add('nomp',TextType::class)
        ->add('duree',TextType::class)
        ->add('createdat',DateType::class)
        ->add('type',TextType::class)
        ->add('valider',SubmitType::class);
        
        $form = $fb ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($projet);
            $em ->flush();
            return $this->redirectToRoute('app_projet');
            
        }
        return $this->render('projet/ajouter.html.twig',
        ['p'=>$form->createView()]);
    }

    #[Route('/sup/{id}', name:'cand_delet')]
    public function delet(Request $request,$id): Response{
        $c = $this ->getDoctrine()
        ->getRepository(Projet::class)
        ->find($id);
        if(!$c){
            throw $this->createNotFoundException(
                'no job found for id'.$id
            );

        }

    $entityManager= $this ->getDoctrine()->getManager();
    $entityManager->remove($c);
    $entityManager->flush();
    return $this->redirectToRoute('app_projet');
    }

    
    #[Route('/edit/{id}', name:'edit_user')]
    public function edit (Request $request,$id){
        $projet = new Projet();
        $projet =$this ->getDoctrine()
        ->getRepository(Projet::class)
        ->find($id);

        if (!$projet){
            throw $this->createNotFoundException(
                'no candidat found for id'.$id
            );
        }
        $fb = $this->createFormBuilder($projet)
        ->add('nomp',TextType::class)
        ->add('duree',TextType::class)
        ->add('createdat',DateType::class)
        ->add('type',TextType::class)
    ->add('Valider', SubmitType::class);
// générer le formulaire à partir du FormBuilder
$form = $fb->getForm();
$form->handleRequest($request);
if ($form->isSubmitted()) {
$entityManager = $this->getDoctrine()->getManager();
$entityManager->flush();
return $this->redirectToRoute('app_projet');
    }
return $this->render('projet/ajouter.html.twig',
['p' => $form->createView()] );
   
}


        
}
