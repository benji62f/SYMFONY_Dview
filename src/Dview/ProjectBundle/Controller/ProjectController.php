<?php

namespace Dview\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dview\ProjectBundle\Entity\Project;
use Dview\ProjectBundle\Entity\Suite;
use Dview\ProjectBundle\Entity\Test;
use Dview\ProjectBundle\Form\ProjectType;
use Dview\ProjectBundle\Form\SuiteType;
use Dview\ProjectBundle\Form\TestType;
use Dview\ProjectBundle\Form\TestFinalizeType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security; // needed to check user's rights
use GuzzleHttp\Client;
use Dview\ProjectBundle\Form\TestForm;
use Dview\ProjectBundle\Form\TestFinalizeForm;
use Dview\MailBundle\Entity\MailConfig;

class ProjectController extends Controller {

    public function projectListingAction(Request $request) {
        $this->buildBreadCrumb(NULL, NULL, NULL);

        $query = $request->query->get('query');
        $page = $request->query->get('page');
        if ($page == NULL) {
            $page = 1;
        }
        if ($page < 1) {
            throw $this->createNotFoundException();
        }
        $em = $this->getDoctrine()->getManager();
        $nbPerPage = 10;

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $listProjects = $em->getRepository('DviewProjectBundle:Project')->getProjects($page, $nbPerPage, $query, NULL);
        } else {
            $listProjects = $em->getRepository('DviewProjectBundle:Project')->getProjects($page, $nbPerPage, $query, $user);
        }

        $nbPages = ceil(count($listProjects) / $nbPerPage);
        if ($page > $nbPages && $page != 1) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }
        return $this->render('DviewProjectBundle:Project:listing.html.twig', array('listProjects' => $listProjects, 'nbPages' => $nbPages, 'page' => $page));
    }

    public function projectViewAction($pid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, true);
        $this->buildBreadCrumb($pid, NULL, NULL);

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('DviewProjectBundle:Project')->find($pid);

        return $this->render('DviewProjectBundle:Project:view.html.twig', array('project' => $project));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function projectAddAction(Request $request) {
        $this->buildBreadCrumb(NULL, NULL, NULL);

        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $mailConfig = new MailConfig();
            $project->setMailConfig($mailConfig);

            $em->persist($project);
            $em->flush();
            
            $project->getMailConfig()->addReceiver($project->getClient()); // set mails receiver (project's client by default)
            $em->flush();

            return $this->redirect($this->generateUrl('dview_project_listing'));
        }
        return $this->render('DviewProjectBundle:Project:add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function projectDeleteAction($pid) {
        $this->buildBreadCrumb($pid, NULL, NULL);

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('DviewProjectBundle:Project')->find($pid);
        $em->remove($project);
        $em->flush();

        return $this->redirect($this->generateUrl('dview_project_listing'));
    }

    public function suiteListingAction($pid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, true);
        $this->buildBreadCrumb($pid, NULL, NULL);

        $em = $this->getDoctrine()->getManager();
        $listSuites = $em->getRepository('DviewProjectBundle:Suite')->findBy(array('project' => $pid), array('date' => 'DESC'));
        return $this->render('DviewProjectBundle:Suite:listing.html.twig', array('pid' => $pid, 'listSuites' => $listSuites));
    }

    public function suiteViewAction($pid, $sid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, true);
        $this->buildBreadCrumb($pid, $sid, NULL);

        $em = $this->getDoctrine()->getManager();
        $suite = $em->getRepository('DviewProjectBundle:Suite')->find($sid);
        return $this->render('DviewProjectBundle:Suite:view.html.twig', array('pid' => $pid, 'suite' => $suite));
    }

    public function suiteDeleteAction($pid, $sid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);
        $this->buildBreadCrumb($pid, $sid, NULL);

        $em = $this->getDoctrine()->getManager();
        $suite = $em->getRepository('DviewProjectBundle:Suite')->find($sid);
        $em->remove($suite);
        $em->flush();

        return $this->redirect($this->generateUrl('dview_suite_listing', array('pid' => $pid)));
    }

    public function suiteAddAction($pid, Request $request) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);
        $this->buildBreadCrumb($pid, NULL, NULL);

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('DviewProjectBundle:Project')->find($pid);

        $suite = new Suite();

        $form = $this->createForm(SuiteType::class, $suite);
        if ($form->handleRequest($request)->isValid()) {
            $suite->setProject($project);

            $mailConfigProject = $project->getMailConfig();
            $suite->setMailConfigExt($mailConfigProject);
            $suite->setMailConfig(new MailConfig());

            $em->persist($suite);
            $em->flush();
            
            $suite->getMailConfig()->addReceiver($project->getClient());
            $em->flush();

            return $this->redirect($this->generateUrl('dview_suite_listing', array('pid' => $pid)));
        }
        return $this->render('DviewProjectBundle:Suite:add.html.twig', array('form' => $form->createView(), 'pid' => $pid));
    }

    public function testListingAction($pid, $sid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, true);
        $this->buildBreadCrumb($pid, $sid, NULL);

        $em = $this->getDoctrine()->getManager();
        $listTests = $em->getRepository('DviewProjectBundle:Test')->findBy(array('suite' => $sid), array('date' => 'DESC'));
        return $this->render('DviewProjectBundle:Test:listing.html.twig', array('pid' => $pid, 'sid' => $sid, 'listTests' => $listTests));
    }

    public function testViewAction(Request $request, $pid, $sid, $tid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, true);
        $this->buildBreadCrumb($pid, $sid, $tid);

        $test = $this->getDoctrine()->getManager()->getRepository('DviewProjectBundle:Test')->find($tid);

        $client = new Client();

        /* variables used for pagination of results */
        $nbPerPage = 10;
        $page = $request->query->get('page');
        if ($page == null) {
            $page = 1;
        }
        $ResNbResults = $client->request('GET', $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb() . '/results/count', [
            'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
            'verify' => false
        ]);
        $nbResults = json_decode($ResNbResults->getBody())->{'count'};

        /* get test on mongodb */
        $res = $client->request('GET', $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb(), [
            'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
            'verify' => false
        ]);
        $mongoTest = json_decode($res->getBody());
        
        /* if the test isn't ready, it needs to be finalized */
        $ready = json_decode($res->getBody())->{'ready'};
        if (!$ready) {
            return $this->redirect($this->generateUrl('dview_test_finalize', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        }

        /* get test's results */
        $res1 = $client->request('GET', $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb() . '/results?filter[limit]=' . $nbPerPage . '&filter[skip]=' . ($page - 1) * $nbPerPage . '&filter[order]=initDate DESC', [
            'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
            'verify' => false
        ]);
        $results = json_decode($res1->getBody());
        foreach ($results as $result) {
            $result->startDate = intval($result->startDate / 1000); // JS timestamp to PHP format
            $result->initDate = intval($result->initDate / 1000);
            $result->capture = $this->container->getParameter('mongo_url') . '/results/' . $result->{'id'} . '/capture';
        }

        /* build image's link from test id */
        $image = $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb() . '/reference';

        if (isset($mongoTest->{'stats'})) {
            $stats = new \stdClass();
            $stats->total = $mongoTest->{'stats'}->{'executions'};
            $stats->passed = $mongoTest->{'stats'}->{'passed'} * 100 / $stats->total; // percentage of success
            $stats->failed = $mongoTest->{'stats'}->{'failed'} * 100 / $stats->total; // percentage of fails
            $stats->error = $mongoTest->{'stats'}->{'error'} * 100 / $stats->total;   // percentage of errors
        } else {
            $stats = NULL;
        }

        $nbPages = ceil($nbResults / $nbPerPage);
        if ($page > $nbPages && $page != 1) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }

        return $this->render('DviewProjectBundle:Test:view.html.twig', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid, 'test' => $mongoTest, 'image' => $image, 'results' => $results, 'stats' => $stats, 'res' => $res->getBody(), 'nbPages' => $nbPages, 'page' => $page));
    }

    public function testAddAction($pid, $sid, Request $request) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);
        $this->buildBreadCrumb($pid, $sid, NULL);

        $em = $this->getDoctrine()->getManager();
        $suite = $em->getRepository('DviewProjectBundle:Suite')->find($sid);
        $project = $em->getRepository('DviewProjectBundle:Project')->find($pid);

        $test = new Test();
        $testForm = new TestForm();

        $form = $this->createForm(TestType::class, $testForm);
        if ($form->handleRequest($request)->isValid()) {
            $client = new Client();
            $body = array(
                'browser' => array(
                    'name' => $form['browser']->getData()->getName(),
                    'version' => $form['browser']->getData()->getVersion(),
                    'width' => $form['width']->getData(),
                    'height' => $form['height']->getData()
                ),
                'page' => $form['page']->getData(),
                "cron" => $form['schedule']->getData()->getCronFormat(),
                "threshold" => $form['threshold']->getData()
            );
            if ($form['zone']->getData() != "") {
                $body['comparison']['zone'] = $form['zone']->getData();
            }
            $res = $client->request('POST', $this->container->getParameter('mongo_url') . '/tests', [
                'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
                'verify' => false,
                'headers' => ['content-type' => 'application/json'],
                'body' => json_encode($body)
            ]);
            $test->setName($form['name']->getData());
            $test->setSuite($suite);
            $test->setIdOnMongoDB(json_decode($res->getBody())->{'id'});

            $test->setMailConfigExt($project->getMailConfig());
            $test->setMailConfig(new MailConfig());

            $em->persist($test);
            $em->flush();

            $tid = $test->getId();

            return $this->redirect($this->generateUrl('dview_test_finalize', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        }
        return $this->render('DviewProjectBundle:Test:add.html.twig', array('form' => $form->createView(), 'pid' => $pid, 'sid' => $sid));
    }

    public function testFinalizeAction($pid, $sid, $tid, Request $request) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);
        $this->buildBreadCrumb($pid, $sid, $tid);

        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('DviewProjectBundle:Test')->find($tid);

        /* get test's ready property on mongodb */
        $client = new Client();
        $res = $client->request('GET', $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb() . '?filter[fields][ready]=true', [
            'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
            'verify' => false
        ]);
        $ready = json_decode($res->getBody())->{'ready'};
        if ($ready) {
            return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        }

        $image = $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb() . '/reference';

        $testFinalizeForm = new TestFinalizeForm();

        $form = $this->createForm(TestFinalizeType::class, $testFinalizeForm);
        if ($form->handleRequest($request)->isValid()) {
            $client = new Client();
            $res = $client->request('PUT', $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb(), [
                'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
                'verify' => false,
                'headers' => ['content-type' => 'application/json'],
                'body' => json_encode(
                        array(
                            'comparison' => array(
                                "zone" => 'coord=' . (int) $form['width']->getData() . 'x' . (int) $form['height']->getData() . '+' . (int) $form['x']->getData() . '+' . (int) $form['y']->getData()
                            )
                        )
                )
            ]);

            $em->flush();

            return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        }

        return $this->render('DviewProjectBundle:Test:finalize.html.twig', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid, 'image' => $image, 'form' => $form->createView()));
    }

    public function testDeleteAction($pid, $sid, $tid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);

        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('DviewProjectBundle:Test')->find($tid);

        $client = new Client();
        $res = $client->request('DELETE', $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb(), [
            'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
            'verify' => false,
            'exceptions' => FALSE
        ]);
        if ($res->getStatuscode() != 500) {
            $em->remove($test);
            $em->flush();
            return $this->redirect($this->generateUrl('dview_test_listing', array('pid' => $pid, 'sid' => $sid)));
        }
        return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
    }

    public function testRunAction($pid, $sid, $tid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);

        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('DviewProjectBundle:Test')->find($tid);

        $client = new Client();
        $res = $client->request('GET', $this->container->getParameter('mongo_url') . '/tests/' . $test->getIdOnMongoDb() . '/run', [
            'auth' => [$this->container->getParameter('mongo_user'), $this->container->getParameter('mongo_pass')],
            'verify' => false,
            'exceptions' => FALSE
        ]);
        return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
    }

    /**
     * Build the bread crumb using projects, suites and tests id
     * 
     * @param type $pid
     * @param type $sid
     * @param type $tid
     */
    public function buildBreadCrumb($pid, $sid, $tid) {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Tableau de bord", "dview_dashboard_homepage");
        $breadcrumbs->addRouteItem("Projets", "dview_project_listing");

        $em = $this->getDoctrine()->getManager();

        if ($pid != null) {
            $project = $em->getRepository("DviewProjectBundle:Project")->find($pid);
            $breadcrumbs->addRouteItem($project->getName(), "dview_suite_listing", array('pid' => $pid));
        }
        if ($sid != null) {
            $suite = $em->getRepository("DviewProjectBundle:Suite")->find($sid);
            $breadcrumbs->addRouteItem($suite->getName(), "dview_test_listing", array('pid' => $pid, 'sid' => $sid));
        }
        if ($tid != null) {
            $test = $em->getRepository("DviewProjectBundle:Test")->find($tid);
            $breadcrumbs->addRouteItem($test->getName(), "dview_test_view", array('pid' => $pid, 'sid' => $sid, 'tid' => $tid));
        }
    }

}
