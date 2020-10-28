<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{

    /**
     * @Route("/",name="app_home")
     */
    public function index(PinRepository $pinRepo): Response
    {
        //Pour enregistrer des données dans la bd
        /*
        $pin = new Pin();
        $pin->setTitle('Title 1');
        $pin->setDescription('Description 1');
        $em->persist($pin);
        $em->flush();
        */

        //Pour récupérer les données enregistrées
        //$pinRepo = $em->getRepository('App\Entity\Pin');
        $pins =  $pinRepo->findAll();
        //dd($pins);

        return $this->render('pins/index.html.twig', [
            'pins' => $pins, //On passe la variable à la vue
        ]);
    }

    /**
     * @Route("/pins/create", name="app_pin_create", methods={"GET","POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        /**
        if($request->isMethod("POST")){
            $datasPinArray = $request->request->all();
            //dd($datasPinArray);

            //ON enregistre le pin dans la bd avec l'Entité manager
            $pin = new Pin();
            $pin->setTitle($datasPinArray["title"]);
            $pin->setDescription($datasPinArray["desc"]);
            $em->persist($pin);
            $em->flush();
            return $this->redirect("/");
        }
         */

        $pin = new Pin();
        $form = $this->createFormBuilder($pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('submit', SubmitType::class, ['label' => 'Créer un pin'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pin);
            $em->flush();
            return $this->redirectToRoute("app_home");
        }
        return $this->render('pins/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("pins/{id<[0-9]*>}",name="app_pin_details")
     */
    public function details(Pin $pin):Response
    {
        //dd($pin);
        return $this->render('pins/details.html.twig', ['pin'=>$pin]);
    }
}
