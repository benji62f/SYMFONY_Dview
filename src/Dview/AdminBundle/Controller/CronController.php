<?php

namespace Dview\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dview\ProjectBundle\Entity\Schedule;
use Dview\AdminBundle\Form\ScheduleType;

class CronController extends Controller {

    public function listingAction() {
        $this->buildBreadCrumb();
        
        $em = $this->getDoctrine()->getManager();
        $listSchedules = $em->getRepository("DviewProjectBundle:Schedule")->findAll();

        return $this->render('DviewAdminBundle:Cron:listing.html.twig', array('listSchedules' => $listSchedules));
    }

    public function addAction(Request $request) {
        $this->buildBreadCrumb();
        
        $schedule = new Schedule();

        $form = $this->createForm(ScheduleType::class, $schedule);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schedule);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_admin_cron_listing'));
        }
        return $this->render('DviewAdminBundle:Cron:add.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($cid) {
        $em = $this->getDoctrine()->getManager();
        $schedule = $em->getRepository("DviewProjectBundle:Schedule")->find($cid);
        if ($schedule == null) {
            throw new NotFoundHttpException();
        }

        $em->remove($schedule);
        $em->flush();

        return $this->redirect($this->generateUrl('dview_admin_cron_listing'));
    }

    public function buildBreadCrumb() {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Tableau de bord", "dview_dashboard_homepage");
        $breadcrumbs->addRouteItem("Table cron", "dview_admin_cron_listing");
    }

}
