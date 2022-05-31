<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\User;
use App\Entity\Medicine;
use App\Form\MedicineType;
use App\Repository\UserRepository;
use App\Repository\MedicineRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/medicines')]
class MedicineController extends AbstractController
{
    #[Route('/', name: 'app_medicine_index', methods: ['GET', 'POST'])]
    public function index(Request $request, MedicineRepository $medicineRepository, UserRepository $userRepository, $page=1, Pdf $knpSnappyPdf): Response
    {
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;      
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
        }
        $user=$userRepository->findOneByEmail($email);
        $medicines= $medicineRepository->getAllMeds();
        $totalMedicinesReturned = $medicines->getIterator()->count();
        $totalMedicines = $medicines->count();
        $maxPages=1;
        if($totalMedicines!=0 && $totalMedicinesReturned!=0){
            $maxPages = ceil($totalMedicines / $totalMedicinesReturned);
        }
        
        if(isset($_POST['makeReport'])){
            $medicines=$medicineRepository->getAllMed();
            $pr_amount=$medicineRepository->getMedStatPr();
            $cn_amount=$medicineRepository->getMedStatCn();
            $count=count( $pr_amount);
            // var_dump($count);die();
            $html =$this->renderView('medicine_report.html.twig', [
                'user' => $user,
                'medicines' => $medicines,
                'in_stocks'=>$pr_amount,
                'boughts'=>$cn_amount,
                'count' =>$count,
            ]);
            $knpSnappyPdf->setOption('encoding', 'utf-8');
            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'Report.pdf',
            );
        }
        return $this->render('medicine/index.html.twig', [
            'medicines' => $medicines,
            'user'=>$user,
            'maxPages' => $maxPages,
            'thisPage'=>$page,
        ]);
    }

    #[Route('/search', name: 'app_medicine_search', methods: ['GET', 'POST'])]
    public function search(Request $request, MedicineRepository $medicineRepository, UserRepository $userRepository, $page=1, Pdf $knpSnappyPdf): Response
    {
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;      
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
        }
        $user=$userRepository->findOneByEmail($email);
        $query=$request->query->get('q');
        $medicines=NULL;
        $maxPages=1;
        if ($query) {
            $medicines= $medicineRepository->searchByQuery($query);
            $totalMedicinesReturned = $medicines->getIterator()->count();
            $totalMedicines = $medicines->count();
            if($totalMedicines!=0 && $totalMedicinesReturned!=0){
                $maxPages = ceil($totalMedicines / $totalMedicinesReturned);
            }
        }
        
        if(isset($_POST['makeReport'])){
            $html =$this->renderView('medicine_report.html.twig', [
                'user' => $user,
                'medicines' => $medicines,
            ]);
            $knpSnappyPdf->setOption('encoding', 'utf-8');
            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'Report.pdf',
            );
        }
        return $this->render('medicine/index.html.twig', [
            'medicines' => $medicines,
            'user'=>$user,
            'maxPages' => $maxPages,
            'thisPage'=>$page,
        ]);
    }

    #[Route('/{page}', name: 'app_medicine_pages_index', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function page_index(Request $request, MedicineRepository $medicineRepository, UserRepository $userRepository, $page=1): Response
    {
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;        
        if($email!=NULL){
            if (!$session->isStarted()) {
                $session->start();
            }
        }
        $user=$userRepository->findOneByEmail($email);
        $medicines= $medicineRepository->getAllMeds($page);
        $totalMedicinesReturned = $medicines->getIterator()->count();
        $totalMedicines = $medicines->count();
        $maxPages=1;
        if($totalMedicines!=0 && $totalMedicinesReturned!=0){
            $maxPages = ceil($totalMedicines / $totalMedicinesReturned);
        }
        return $this->render('medicine/index.html.twig', [
            'medicines' =>$medicines,
            'user'=>$user,
            'maxPages' => $maxPages,
            'thisPage'=>$page,
        ]);
    }

    #[Route('/new', name: 'app_medicine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MedicineRepository $medicineRepository): Response
    {
        $medicine = new Medicine();
        $form = $this->createForm(MedicineType::class, $medicine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medicineRepository->add($medicine, true);

            return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('medicine/new.html.twig', [
            'medicine' => $medicine,
            'form' => $form,
        ]);
    }

    #[Route('/medicine/{id}', name: 'app_medicine_show', methods: ['GET'])]
    public function show(Request $request, ManagerRegistry $doctrine, Medicine $medicine): Response
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
        return $this->render('medicine/show.html.twig', [
            'user'=>$user,
            'medicine' => $medicine,
        ]);
    }

    #[Route('/medicine/{id}/edit', name: 'app_medicine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Medicine $medicine, MedicineRepository $medicineRepository): Response
    {
        $form = $this->createForm(MedicineType::class, $medicine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medicineRepository->add($medicine, true);

            return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('medicine/edit.html.twig', [
            'medicine' => $medicine,
            'form' => $form,
        ]);
    }

    #[Route('/medicine/{id}', name: 'app_medicine_delete', methods: ['POST'])]
    public function delete(Request $request, Medicine $medicine, MedicineRepository $medicineRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medicine->getId(), $request->request->get('_token'))) {
            $medicineRepository->remove($medicine, true);
        }

        return $this->redirectToRoute('app_medicine_index', [], Response::HTTP_SEE_OTHER);
    }
}
