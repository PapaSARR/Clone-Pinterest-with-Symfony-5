<?php

namespace App\Controller;


use App\Entity\Pin;
use App\Form\PinType;
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
    private $em, $pinRepo;

    public function __construct(EntityManagerInterface $em, PinRepository $pinRepo){
    $this->em = $em;
    $this->pinRepo = $pinRepo;
    }

    /**
     * @Route("/",name="app_home")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
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
        // Recover all pins by descending order according to createdAt field
        $pins =  $this->pinRepo->findBy([],['createdAt'=>'DESC']);

        return $this->render('pins/index.html.twig', [
            'pins' => $pins, //On passe la variable à la vue
        ]);
    }

    /**
     * @Route("/pins/create", name="app_pin_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
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
        /*
        $form = $this->createFormBuilder($pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            //->add('submit', SubmitType::class, ['label' => 'Créer un pin'])
            ->getForm();
        */
        //Creation of the form in src/Form/PinType class with make:form command to avoid repetitions in the code
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $this->em->persist($pin);
            $this->em->flush();
            $this->addFlash('success','Pin successfully created');
            return $this->redirectToRoute("app_home");
        }
        return $this->render('pins/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("pins/{id<[0-9]*>}",name="app_pin_details", methods={"GET"})
     */
    public function details(Pin $pin):Response
    {
        //dd($pin);
        return $this->render('pins/details.html.twig', ['pin'=>$pin]);
    }

    /**
     * @Route("pins/{id<[0-9]*>}/edit",name="app_pin_edit", methods={"GET","PUT"})
     */
    public function edit(Request $request, Pin $pin):Response
    {
        /*
        $form = $this->createFormBuilder($pin)
        ->add('title', TextType::class)
        ->add('description', TextareaType::class)
        ->getForm();
        */
        $form = $this->createForm(PinType::class, $pin, ['method'=>'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success','Pin successfully updated');
            return $this->redirectToRoute("app_home");
        }
        return $this->render('pins/edit.html.twig', ['pin'=>$pin, 'form'=>$form->createView()]);
    }

    /**
     * @Route("pins/{id<[0-9]*>}",name="app_pin_delete", methods={"DELETE"})
     */
    public function delete(Request $request,Pin $pin):Response
    {
        //dd($request->request->get('csrf_token'));

        if($this->isCsrfTokenValid('pin_csrf', $request->request->get('csrf_token'))){
            $this->em->remove($pin);
            $this->em->flush();
            $this->addFlash('danger','Pin successfully deleted');
        }
        return $this->redirectToRoute("app_home");

    }
}
