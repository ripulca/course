<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Custom;
use App\Form\CustomType;
use App\Repository\CustomRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/custom')]
class CustomController extends AbstractController
{
    #[Route('/', name: 'app_custom_index', methods: ['GET'])]
    public function index(Request $request, ManagerRegistry $doctrine, CustomRepository $customRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;      
        $user=$entityManager->getRepository(User::class)->findOneByEmail($email);
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
            $customs=$user->getCustoms();
        }
        else{
            $customs=$customRepository->findAll();
        }
        
        return $this->render('custom/index.html.twig', [
            'user'=>$user,
            'customs' => $customs,
        ]);
    }

    #[Route('/new', name: 'app_custom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CustomRepository $customRepository): Response
    {
        $custom = new Custom();
        $form = $this->createForm(CustomType::class, $custom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customRepository->add($custom, true);

            return $this->redirectToRoute('app_custom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('custom/new.html.twig', [
            'custom' => $custom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_custom_show', methods: ['GET'])]
    public function show(Request $request, ManagerRegistry $doctrine, Custom $custom): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;      
        $user=$entityManager->getRepository(User::class)->findOneByEmail($email);
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
        }
        return $this->render('custom/show.html.twig', [
            'user'=>$user,
            'custom' => $custom,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_custom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ManagerRegistry $doctrine, Custom $custom, CustomRepository $customRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;      
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
        }
        $user=$entityManager->getRepository(User::class)->findOneByEmail($email);
        $role=$user->getRoles();
        $role=$role[0];
        $customers=$entityManager->getRepository(User::class)->findByRole('ROLE_CUSTOMER');
        if (isset($_POST["address"])) {
            // $form = $_POST["custom"];
            if($role=="ROLE_DOCTOR"){
                $customer_id=$_POST['customer'];
                $customer=$entityManager->getRepository(User::class)->findOneById($customer_id);
            }
            else{
                $customer=$user;
            }
            $address=$_POST['address'];
            $custom->setAddress($address);
            if(isset($_POST["courier"])){
                $couriers=$entityManager->getRepository(User::class)->findCourierWithCountCustoms($customer->getCity());
                
                if($couriers){
                    $courier_id=$couriers[0]['id'];
                    $courier=$entityManager->getRepository(User::class)->findOneById($courier_id);
                    $custom->setCourier($courier);
                }
                else{
                    echo 'so far there is no delivery in your city';
                }
            }
            $custom->setIsInCart(false);
            $customRepository->add($custom, true);
            return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
        }
        // var_dump($customers);
        return $this->renderForm('custom/edit.html.twig', [
            'user'=>$user,
            'customers'=>$customers,
            'custom' => $custom,
        ]);
    }

    #[Route('/{id}', name: 'app_custom_delete', methods: ['POST'])]
    public function delete(Request $request, Custom $custom, CustomRepository $customRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$custom->getId(), $request->request->get('_token'))) {
            $customRepository->remove($custom, true);
        }

        return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
    }
}
