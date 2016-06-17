<?php

namespace Dview\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dview\ProjectBundle\Entity\Browser;
use Dview\AdminBundle\Form\BrowserType;

class BrowserController extends Controller {

    public function listingAction() {
        $this->buildBreadCrumb();
        
        $em = $this->getDoctrine()->getManager();
        $listBrowsers = $em->getRepository("DviewProjectBundle:Browser")->findAll();

        return $this->render('DviewAdminBundle:Browser:listing.html.twig', array('listBrowsers' => $listBrowsers));
    }

    public function addAction(Request $request) {
        $this->buildBreadCrumb();
        
        $browser = new Browser();

        $form = $this->createForm(BrowserType::class, $browser);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($browser);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_admin_browser_listing'));
        }
        return $this->render('DviewAdminBundle:Browser:add.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($bid) {
        $em = $this->getDoctrine()->getManager();
        $browser = $em->getRepository("DviewProjectBundle:Browser")->find($bid);
        if ($browser == null) {
            throw new NotFoundHttpException();
        }

        $em->remove($browser);
        $em->flush();

        return $this->redirect($this->generateUrl('dview_admin_browser_listing'));
    }

    public function buildBreadCrumb() {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Tableau de bord", "dview_dashboard_homepage");
        $breadcrumbs->addRouteItem("Gestion des navigateurs", "dview_admin_browser_listing");
    }

}
