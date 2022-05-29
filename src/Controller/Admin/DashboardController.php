<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Custom;
use App\Entity\Doctor;
use App\Entity\Hospital;
use App\Entity\Medicine;
use App\Entity\Provider;
use App\Entity\Provides;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\MedicineCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url=$adminUrlGenerator->setController(MedicineCrudController::class)->generateUrl();
        return $this->redirect($url);
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pharmsoft')
            ->disableUrlSignatures();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Medicines', 'fas fa-list-alt', Medicine::class);
        yield MenuItem::linkToCrud('Providers', 'fas fa-list-alt', Provider::class);
        yield MenuItem::linkToCrud('Provides', 'fas fa-list-alt', Provides::class);
        yield MenuItem::linkToCrud('Hospital', 'fas fa-list-alt', Hospital::class);
        yield MenuItem::linkToCrud('Customs', 'fas fa-list-alt', Custom::class);
        yield MenuItem::linkToCrud('Doctors', 'fas fa-user', Doctor::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
