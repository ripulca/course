<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\User;
use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\HospitalRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/hospital')]
class HospitalController extends AbstractController
{
    #[Route('/', name: 'app_hospital_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ManagerRegistry $doctrine, HospitalRepository $hospitalRepository, Pdf $knpSnappyPdf): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;
        if (!$session->isStarted()) {
            $session->start();
        }
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
        
        if(isset($_POST['makeReport'])){
            $html =$this->renderView('hospital/index.html.twig', [
                'user'=>$user,
                'hospitals' => $hospitalRepository->findAll(),
            ]);
            $knpSnappyPdf->setOption('encoding', 'utf-8');
            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'Report.pdf',
            );
        }
        return $this->render('hospital/index.html.twig', [
            'user'=>$user,
            'hospitals' => $hospitalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hospital_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $hospital = new Hospital();
        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospitalRepository->add($hospital, true);

            return $this->redirectToRoute('app_hospital_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hospital/new.html.twig', [
            'hospital' => $hospital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hospital_show', methods: ['GET'])]
    public function show(Request $request, ManagerRegistry $doctrine, Hospital $hospital): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;
        if (!$session->isStarted()) {
            $session->start();
        }
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
        return $this->render('hospital/show.html.twig', [
            'user'=>$user,
            'hospital' => $hospital,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hospital_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ManagerRegistry $doctrine, Hospital $hospital, HospitalRepository $hospitalRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $request->getSession();
        $email = $session->get(Security::LAST_USERNAME) ?? null;
        if (!$session->isStarted()) {
            $session->start();
        }
        $user=$entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospitalRepository->add($hospital, true);

            return $this->redirectToRoute('app_hospital_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hospital/edit.html.twig', [
            'user'=>$user,
            'hospital' => $hospital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hospital_delete', methods: ['POST'])]
    public function delete(Request $request, Hospital $hospital, HospitalRepository $hospitalRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hospital->getId(), $request->request->get('_token'))) {
            $hospitalRepository->remove($hospital, true);
        }

        return $this->redirectToRoute('app_hospital_index', [], Response::HTTP_SEE_OTHER);
    }
}
