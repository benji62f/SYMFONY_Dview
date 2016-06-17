<?php

namespace Dview\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller {

    public function indexAction() {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $nbProjects = $em->getRepository('DviewProjectBundle:Project')->getCount(NULL);
            $nbSuites = $em->getRepository('DviewProjectBundle:Suite')->getCount(NULL);
            $nbTests = $em->getRepository('DviewProjectBundle:Test')->getCount(NULL);
        } else {
            $nbProjects = $em->getRepository('DviewProjectBundle:Project')->getCount($user);
            $nbSuites = $em->getRepository('DviewProjectBundle:Suite')->getCount($user);
            $nbTests = $em->getRepository('DviewProjectBundle:Test')->getCount($user);
        }
        
        $nbUsers = $em->getRepository('DviewUserBundle:User')->getCount();
            
        return $this->render('DviewDashboardBundle:Default:index.html.twig', array('nbProjects' => $nbProjects, 'nbSuites' => $nbSuites, 'nbTests' => $nbTests, 'nbUsers' => $nbUsers));
    }

}
