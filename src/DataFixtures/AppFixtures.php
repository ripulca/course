<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Custom;
use App\Entity\Doctor;
use App\Entity\Contains;
use App\Entity\Hospital;
use App\Entity\Medicine;
use App\Entity\Provider;
use App\Entity\Provides;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private $roles=[['ROLE_CUSTOMER'], ['ROLE_COURIER'], ['ROLE_DOCTOR'], ['ROLE_ADMIN']];
    private $email=[ 'CUSTOMER_', 'COURIER_', 'DOCTOR_', 'ADMIN_'];
    private $cities=['Москва', 'Москва', 'Омск', 'Липецк'];
    private $names=['Юрий', 'Алиса', 'Елена', 'Сергей'];
    private $surnames=['Юрий', 'Алиса', 'Елена', 'Сергей'];
    private $phones=['89042349626', '76542884425', '88879773525', '88005553535'];
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; $i++) {        //users
            $user = new User();
            $user->setEmail($this->email[$i].'user@example.com');
            $user->setPassword(password_hash('pwd____'.$i, PASSWORD_DEFAULT));
            $user->setName($this->names[$i]);
            $user->setSurname($this->surnames[$i]);
            $user->setRoles($this->roles[$i]);
            $user->setCity($this->cities[$i]);
            $user->setPhone($this->phones[$i]);
            $manager->persist($user);

            if ($i==0) {
                $customer=$user;
            }

            $provider = new Provider();      //providers
            $provider->setName('provider_'.$i);
            $provider->setPhone($this->phones[$i]);
            $provider->setEmail($this->email[$i].'provider@example.com');
            $manager->persist($provider);

            for($j=0;$j<2;$j++){      //meds
                $medicine = new Medicine();
                $medicine->setName('med_'.$j);
                $medicine->setPrice('1'.$j);
                $medicine->setCompound('compound_'.$j);
                $medicine->setPharmgroup('pharmgroup_'.$j);
                $medicine->setAction('action_'.$j);
                $manager->persist($medicine);

                $provides = new Provides();
                $provides->setProvider($provider);
                $provides->setMedicine($medicine);
                $provides->setAmount($j+1);
                $manager->persist($provides);

                $custom = new Custom();      //customs
                $custom->setPrice('1'.$j);
                $date = new \DateTime();
                $date->add(new \DateInterval('P7D'));
                $custom->setPaymentDate($date);
                $custom->setCompleteDate($date);
                $custom->setCustomer($customer);
                $custom->setIsInCart(false);
                $custom->setAddress('address'.$j);
                $manager->persist($custom);

                $contains = new Contains();      //contains
                $contains->setCustom($custom);
                $contains->setMedicine($medicine);
                $contains->setAmount($j+1);
                $manager->persist($contains);
            }
            if($i==1){
                $hospital= new Hospital();
                $hospital->setName('Областная больница №1');
                $hospital->setCity('Омск');
                $hospital->setPhone($this->phones[$i]);
                $hospital->setEmail($this->email[$i].'hospital@example.com');
                $manager->persist($hospital);

                $doctor = new Doctor();    //doctor
                $doctor->setUserProfile($user);
                $doctor->setHospital($hospital);
                $doctor->setSpecialization('Терапевт');
                $doctor->setPost('Заведующий(-ая) отделением Терапевтической службы');
                $manager->persist($doctor);
            }          
        }
        $manager->flush();
    }
}
