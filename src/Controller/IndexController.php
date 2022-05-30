<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Custom;
use App\Entity\Contains;
use App\Entity\Medicine;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;
        if (!$session->isStarted()) {
            $session->start();
        }
        $user=$userRepository->findOneBy(['email'=>$email]);
        
        return $this->render('index.html.twig', [
            'user'=>$user,
        ]);
    }

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function showCart(Request $request, ManagerRegistry $doctrine): Response
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
        $doctor=$user->getDoctor();
        if($role[0]=='ROLE_CUSTOMER'){
            $cur_custom=$entityManager->getRepository(Custom::class)->findOneBy([
                'customer'=>$user,
                'is_in_cart'=>true,
            ]);
        }
        else if($role[0]=='ROLE_DOCTOR'){
            $cur_custom=$entityManager->getRepository(Custom::class)->findOneBy([
                'doctor'=>$doctor,
                'is_in_cart'=>true,
            ]);
        }
        if ($cur_custom) {
            $medicines=$entityManager->getRepository(Contains::class)->getMedsListByCustom($cur_custom);
        }
        else{
            return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('cart/cart.html.twig', [
            'user'=>$user,
            'custom'=>$cur_custom,
            'medicines'=>$medicines,
        ]);
    }

    #[Route('/cart/{id}', name: 'delete_medicine_from_cart', methods: ['POST'])]
    public function deleteFromCart(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $contains=$entityManager->getRepository(Contains::class)->findOneById($id);
        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $entityManager->getRepository(Contains::class)->remove($contains, true);
        }

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cart/new/{id}', name: 'add_medicine_to_cart', methods: ['POST'])]
    public function toCart(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;      
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
        }
        $medicine=$entityManager->getRepository(Medicine::class)->findOneById($id);
        $user=$entityManager->getRepository(User::class)->findOneByEmail($email);
        $role=$user->getRoles();
        $doctor=$user->getDoctor();
        if ($role[0]=='ROLE_CUSTOMER') {
            $cur_custom=$entityManager->getRepository(Custom::class)->findOneBy([
                'customer'=>$user,
                'is_in_cart'=>true,
            ]);}
        else if ($role[0]=='ROLE_DOCTOR'){
            $cur_custom=$entityManager->getRepository(Custom::class)->findOneBy([
                'doctor'=>$doctor,
                'is_in_cart'=>true,
            ]);
        }
        if($cur_custom){
            $if_contains=$entityManager->getRepository(Contains::class)->findOneBy([
                'medicine'=>$medicine,
                'custom'=>$cur_custom,
            ]);
            if($if_contains){
                $cur_amount=$if_contains->getAmount();
                $if_contains->setAmount($cur_amount+1);
                $entityManager->getRepository(Contains::class)->add($if_contains, true);
                return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
            }
            else{
                $contains=new Contains();
                $contains->setCustom($cur_custom);
                $contains->setMedicine($medicine);
                $contains->setAmount(1);
                $entityManager->getRepository(Contains::class)->add($contains, true);
            }
            $cur_custom->setPrice($cur_custom->getPrice()+$medicine->getPrice());
            $entityManager->getRepository(Custom::class)->add($cur_custom, true);
        }
        else{
            $custom=new Custom();
            if ($role[0]=='ROLE_DOCTOR') {
                $customer=$entityManager->getRepository(User::class)->findByRole('ROLE_CUSTOMER');
                $customer=$customer[0];
                $custom->setDoctor($user->getDoctor());
            }
            else{
                $customer=$user;
            }
            $custom->setPrice($medicine->getPrice());
            $date = new \DateTime();
            $date->add(new \DateInterval('P7D'));
            $custom->setPaymentDate($date);
            $custom->setCompleteDate($date);
            $custom->setCustomer($customer);
            $custom->setIsInCart(true);
            $custom->setIsReady(false);
            $custom->setAddress($customer->getCity());
            $entityManager->getRepository(Custom::class)->add($custom, true);
            $contains=new Contains();
            $contains->setCustom($custom);
            $contains->setMedicine($medicine);
            $contains->setAmount(1);
            $entityManager->getRepository(Contains::class)->add($contains, true);
        }
        return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
    }
}
