<?php
/**
 * Created by PhpStorm.
 * User: ceirokilp
 * Date: 27/09/2018
 * Time: 15:38
 */

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    public function register(UserPasswordEncoderInterface $userPasswordEncoder, Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->redirect('articles_index');
        }
        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}