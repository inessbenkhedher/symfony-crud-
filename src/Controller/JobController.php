<?php

namespace App\Controller;

use App\Entity\Candidature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Job;
use App\Entity\Image;

class JobController extends AbstractController
{
    #[Route('/job', name: 'app_job')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager = $this->getDoctrine()->getManager(); $job = new Job(); $job->setType('Architecte');
        $job->setCompany ('poly');
        $job->setDescription('Genie logiciel');
        $job->setExpiresAt(new \DateTimeImmutable());
        $job->setEmail('iness@gmail.com');
        $image = new Image();
        $image ->setUrl('https://cdn.pixabay.com/photo/2015/10/30/10/03/gold-1013618_960_720.jpg
        ');
        $image -> setAlt('Job de reves');
        $job -> setImage($image);

        // ajout condidat
        $candidature1=new Candidature();
        $candidature1 -> setCandidat("Rhaim");
        $candidature1 -> setContenu("formation J2EE");
        $candidature1 -> setDate(new \DateTime());

        $candidature2=new Candidature();
        $candidature2 -> setCandidat("Salima");
        $candidature2 -> setContenu("formation Symfony");
        $candidature2 -> setDate(new \DateTime());
        
        $candidature1 -> setJob($job);
        $candidature2 -> setJob($job);

        $entityManager ->persist($job);
        $entityManager ->persist($candidature1);
        $entityManager ->persist($candidature2);

        $entityManager->persist($job -> getImage());

        $entityManager->persist($job);
        $entityManager->flush();

        return $this->render('job/index.html.twig', [
            'id' => $job ->getId(),
        ]);
    }
   
    #[Route('/job/{id}', name:'job_show')]

    public function show($id){
        $job = $this->getDoctrine()
            ->getRepository(Job::class)
             ->find($id);
             $em=$this->getDoctrine()->getManager();
             $listCandidatures=$em->getRepository(Candidature::class)->
             findBy(['Job'=>$job]);
       
             if (!$job) {
                throw $this->createNotFoundException(
                'No job found for id '.$id
                );
                }
        return $this->render('job/show.html.twig', [
            'listCandidatures'=> $listCandidatures,
                'job' =>$job
                ]);
               
    }


    #[Route('/Ajouter', name:'Ajouter')]
    public function ajouter(Request $request){
        $candidat = new Candidature();
        $fb = $this -> createFormBuilder($candidat)
        ->add('candidat',TextType::class)
        ->add('contenu',TextType::class,array("label"=>"Contenu"))
        ->add('date',DateType::class)
        ->add('job',EntityType::class,[
            'class' => Job::class,
            'choice_label' =>'type',
        ])
        ->add('valider',SubmitType::class);
        
        $form = $fb ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($candidat);
            $em ->flush();
            return $this -> redirectToRoute('Acceuil');
        }


        return $this->render('job/ajouter.html.twig',
        ['f'=>$form->createView()]);
    }

    #[Route('/Add', name:'ajout_job')]
    public function ajouter2(Request $request){
        $job=new Job();
        
        $form = $this ->createForm("App\Form\JobType",$job);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em ->flush();
            return $this -> redirectToRoute('Acceuil');
        }


        return $this->render('job/ajouter.html.twig',
        ['f'=>$form->createView()]);
    }

    #[Route('/', name:'home')]
    public function home(Request $request){
        $form = $this ->createFormBuilder()
        ->add("critere",TextType::class)
        ->add("Valider",SubmitType::class)
        ->getForm();
        $form ->handleRequest($request);
       
     
            $em = $this->getDoctrine()->getManager();
            $repo=$em->getRepository(Candidature::class);
            $lesCandidats = $repo->findAll();
            if ($form->isSubmitted()){
                $data = $form ->getData();
                $lesCandidats = $repo ->recherche($data['critere']);
            }
            
            return $this->render('job/home.html.twig',
            ['lesCandidats'=>$lesCandidats,'form'=>$form->createView()]);
      
        }
        

        #[Route('/supp/{id}', name:'cand_delete')]
        public function delete(Request $request,$id): Response{
            $c = $this ->getDoctrine()
            ->getRepository(Candidature::class)
            ->find($id);
            if(!$c){
                throw $this->createNotFoundException(
                    'no job found for id'.$id
                );

            }

        $entityManager= $this ->getDoctrine()->getManager();
        $entityManager->remove($c);
        $entityManager->flush();
        return $this->redirectToRoute('home');
        }


     /**
* @Route("/editU/{id}", name="edit_user")
* Method({"GET","POST"})
*/


        public function edit (Request $request,$id){
            $candidat = new Candidature();
            $candidat =$this ->getDoctrine()
            ->getRepository(Candidature::class)
            ->find($id);

            if (!$candidat){
                throw $this->createNotFoundException(
                    'no candidat found for id'.$id
                );
            }
            $fb = $this->createFormBuilder($candidat)
            ->add('candidat', TextType::class)
            ->add('contenu', TextType::class, array("label" => "Contenu"))
            ->add('date', DateType::class)
            ->add('job', EntityType::class, [
            'class' => Job::class,
             'choice_label' => 'type',
         ])
        ->add('Valider', SubmitType::class);
    // générer le formulaire à partir du FormBuilder
    $form = $fb->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();
    return $this->redirectToRoute('home');
        }
    return $this->render('job/ajouter.html.twig',
    ['f' => $form->createView()] );
       
}

#[Route('/listjobs', name:'list')]
public function list(){
    
        $em = $this->getDoctrine()->getManager();
        $repo=$em->getRepository(Job::class);
        $lesJob = $repo->findAll();
    
        return $this->render('job/list.html.twig',
        ['lesJob'=>$lesJob]);
    }
    


}
