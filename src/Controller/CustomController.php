<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\User;
use App\Entity\Custom;
use App\Entity\Contains;
use App\Entity\Provides;
use App\Form\CustomType;
use App\Repository\CustomRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/custom')]
class CustomController extends AbstractController
{
    #[Route('/', name: 'app_custom_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ManagerRegistry $doctrine, CustomRepository $customRepository, Pdf $knpSnappyPdf): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;      
        $user=$entityManager->getRepository(User::class)->findOneByEmail($email);
        $customers=NULL;
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
            $role=$user->getRoles();
            $role=$role[0];
            if($role=='ROLE_CUSTOMER'){
                $customs=$user->getCustoms();
            }
            else if($role=='ROLE_DOCTOR'){
                $customs=$user->getDoctor()->getCustoms();
                $customers=$entityManager->getRepository(User::class)->getDocCustomers($user->getDoctor());
            }
            else{
                $customs=$entityManager->getRepository(Custom::class)->getCustomsToDeliver($user->getId());
            }
        }
        else{
            $customs=$customRepository->findAll();
        }
        
        if(isset($_POST['makeReport'])){
            $html =$this->renderView('custom_report.html.twig', [
                'user' => $user,
                'customs' => $customs,
            ]);
            $knpSnappyPdf->setOption('encoding', 'utf-8');
            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'Report.pdf',
            );
        }

        return $this->render('custom/index.html.twig', [
            'customers' => $customers,
            'user'=>$user,
            'customs' => $customs,
        ]);
    }
    #[Route('/history', name: 'get_customer_history', methods: ['GET', 'POST'])]
    public function search(Request $request, ManagerRegistry $doctrine, Pdf $knpSnappyPdf): Response
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
        $customers=$entityManager->getRepository(User::class)->findByRole('ROLE_CUSTOMER');
        $customer=$entityManager->getRepository(User::class)->findOneById($request->query->get('customer'));
        $customs=$entityManager->getRepository(Custom::class)->findBy([
            'customer' => $customer,
            'doctor' =>$user->getDoctor(),
        ]);
        
        if(isset($_POST['makeReport'])){
            $html =$this->renderView('custom_report.html.twig', [
                'customs' => $customs,
            ]);
            $knpSnappyPdf->setOption('encoding', 'utf-8');
            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'Report.pdf',
            );
        }

        return $this->render('custom/index.html.twig', [
            'customers' => $customers,
            'user'=>$user,
            'customs' => $customs,
        ]);
    }

    #[Route('/ready/{id}', name: 'app_custom_ready', methods: ['POST'])]
    public function makeReady(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $custom=$entityManager->getRepository(Custom::class)->findOneById($id);
        $contains=$custom->getContains();
        foreach($contains as $contain){
            $con_am=$contain->getAmount();
            $provides=$entityManager->getRepository(Provides::class)->findBy(['medicine'=>$contain->getMedicine()]);
            foreach($provides as &$provide){
                if($provide->getAmount()>=$con_am){
                    $provide->setAmount($provide->getAmount()-$con_am);
                }
                else{
                    $con_am=$con_am-$provide->getAmount();
                    $provide->setAmount(0);
                }
                $entityManager->getRepository(Provides::class)->add($provide, true);
            }
        }
        if($custom->getCompleteDate()==$custom->getPaymentDate()){
            $custom->setPaymentDate(new \DateTime());
        }
        $custom->setCompleteDate(new \DateTime());
        $custom->setIsReady(true);
        $entityManager->getRepository(Custom::class)->add($custom, true);
        return $this->redirectToRoute('app_custom_index', [], Response::HTTP_SEE_OTHER);
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
        $medicines=$entityManager->getRepository(Contains::class)->getMedsListByCustom($custom);
        return $this->render('custom/show.html.twig', [
            'user'=>$user,
            'custom' => $custom,
            'medicines' => $medicines
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
                $customer=$entityManager->getRepository(User::class)->findOneById($_POST['customer']);
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
            $date = new \DateTime();
            $add_date = new \DateTime();
            $add_date->add(new \DateInterval('P7D'));
            if(isset($_POST['payment'])){
                $custom->setPaymentDate($date);
                $custom->setCompleteDate($add_date);
            }
            else{
                $custom->setCompleteDate($add_date);
                $custom->setPaymentDate($add_date);
            }
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
