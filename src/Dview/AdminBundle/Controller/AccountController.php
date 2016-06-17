<?php

namespace Dview\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Dview\AdminBundle\Form\UserType;
use Dview\AdminBundle\Entity\UserForm;

class AccountController extends Controller {

    public function listingAction() {
        $this->buildBreadCrumb(NULL);
        $em = $this->getDoctrine()->getManager();
        $listUsers = $em->getRepository("DviewUserBundle:User")->findAll();

        return $this->render('DviewAdminBundle:Account:listing.html.twig', array('listUsers' => $listUsers));
    }

    public function editAction($uid, Request $request) {
        $user = $this->getDoctrine()->getManager()->getRepository("DviewUserBundle:User")->find($uid);
        if ($user == null) {
            throw new NotFoundHttpException();
        }

        $this->buildBreadCrumb($uid);

        $userForm = new UserForm($user);

        $form = $this->createForm(UserType::class, $userForm);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array('id' => $user->getId()));

            $user->setUsername($form['username']->getData());
            $user->setRoles(array($form['role']->getData()));
            $user->setEmail($form['email']->getData());
            if ($form['password']->getData() != '') {
                $user->setPlainPassword($form['password']->getData());
            }

            $userManager->updateUser($user);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_admin_account_listing'));
        }

        return $this->render('DviewAdminBundle:Account:edit.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($uid) {
        $user = $this->getDoctrine()->getManager()->getRepository("DviewUserBundle:User")->find($uid);
        if ($user == null) {
            throw new NotFoundHttpException();
        }

        $userManager = $this->container->get('fos_user.user_manager');

        try {
            $userManager->deleteUser($user);
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('dview_admin_account_listing', array('error' => true)));
        }

        return $this->redirect($this->generateUrl('dview_admin_account_listing'));
    }

    public function buildBreadCrumb($uid) {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Tableau de bord", "dview_dashboard_homepage");
        $breadcrumbs->addRouteItem("Comptes d'utilisateurs", "dview_admin_account_listing");
        if ($uid != NULL) {
            $user = $this->getDoctrine()->getManager()->getRepository("DviewUserBundle:User")->find($uid);
            $breadcrumbs->addRouteItem($user->getUserName(), "fos_user_profile_show");
        }
    }

}
