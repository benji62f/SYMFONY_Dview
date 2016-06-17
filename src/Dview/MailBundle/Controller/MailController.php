<?php

namespace Dview\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dview\MailBundle\Form\MailType;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller {

    public function sendAction() {
        $content = $this->get('request')->getContent();
        if (!empty($content)) {
            $params = json_decode($content); // 2nd param to get as array
            $params->initDate = intval($params->initDate / 1000);
        } else {
            throw new HttpException(422, 'Missing arguments');
        }

        $test = $this->getDoctrine()->getManager()->getRepository('DviewProjectBundle:Test')->getTest($params->testId);
        if ($test == NULL) {
            throw new HttpException(404, "Test wasn't found");
        }

        if ($test->getUseMailConfigExt()) {
            $config = $test->getMailConfigExt();
        } else {
            $config = $test->getMailConfig();
        }

        if ($config == NULL) {
            throw new HttpException(500, 'Mail service not configured for this test');
        }

        $suite = $test->getSuite();
        $project = $suite->getProject();

        if ($params->status === 2) {
            $status = 'OK';
        } else if ($params->status === 3) {
            $status = 'KO';
        } else if ($params->status === 4) {
            $status = 'N/A';
        }

        /*
         * Put values of 'mail variables' in the 'additional content' mail section
         */
        $additionalContent = $config->getAdditionalContent();
        $additionalContent = preg_replace(
                array('#\$status#', '#\$percentage#', '#\$date#', '#\$time#',
            '#\$browser#', '#\$version#', '#\$os#', '#\$project#', '#\$suite#', '#\$name#'), array($status, $params->misMatchPercentage,
            date('d/m/Y H:i:s', $params->initDate), $params->runtime, $params->browser->name,
            $params->browser->version, $params->browser->platform,
            $project->getName(), $suite->getName(), $test->getName()
                )
                , $additionalContent);

        /*
         * Prepare message
         */
        if (($params->status === 2 && $config->getOnOK()) || ($params->status != 2 && $config->getOnKO())) {
            $message = \Swift_Message::newInstance()
                    ->setSubject("[Dview] Rapport d'exécution du test " . $test->getName())
                    ->setFrom('noreply@dview.com')
                    ->setBody(
                    $this->renderView(
                            'DviewMailBundle:Mail:result.html.twig', array('result' => $params, 'test' => $test, 'suite' => $suite, 'project' => $project, 'withAdditionalContent' => $config->getWithAdditionalContent(), 'additionalContent' => $additionalContent, 'withStatus' => $config->getWithStatus(), 'withInfo' => $config->getWithInfo())
                    ), 'text/html'
                    )
            ;

            if ($config->getCustomObject() != NULL) {
                $message->setSubject($config->getCustomObject());
            }

            /*
             * Send mail to all receivers
             */
            foreach ($config->getReceivers() as $receiver) {
                $message->setTo($receiver->getEmail());
                $this->get('mailer')->send($message);
            }
        } else {
            throw new HttpException(204, 'Mail not desired'); // if the mail service is disabled by the client (in test's settings)
        }

        return new Response();
    }

    /*
     * Curl cmd line to test the feature
     */

//    curl -X POST -d '{"_id":"574ce33cb7789ed86a13ff37","_test":"574c028ee90c190d613e4981","misMatchPercentage":0,"startDate":1464656714555,"initDate":1464656700751,"__v":0,"browser":{"name":"firefox","version":"46.0.1","platform":"LINUX"},"status":2,"runtime":0}' dview.local/app_dev.php/mail

    public function projectEditAction(Request $request, $pid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('DviewProjectBundle:Project')->find($pid);
        $mail = $project->getMailConfig();
        $content = $mail->getAdditionalContent(); // Custom mail body

        $form = $this->createForm(MailType::class, $mail);
        if ($form->handleRequest($request)->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('dview_project_view', array('pid' => $pid)));
        }
        return $this->render('DviewMailBundle::project_edit.html.twig', array('form' => $form->createView(), 'content' => $content, 'pid' => $pid));
    }

    public function suiteEditAction(Request $request, $pid, $sid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);

        $em = $this->getDoctrine()->getManager();

        $suite = $em->getRepository('DviewProjectBundle:Suite')->find($sid);
        $mail = $suite->getMailConfig();
        $content = $mail->getAdditionalContent(); // Custom mail body

        if ($request->query->get('ext')) {
            $suite->setUseMailConfigExt(true);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_suite_view', array('pid' => $pid, 'sid' => $sid)));
        }

        $form = $this->createForm(MailType::class, $mail);
        if ($form->handleRequest($request)->isValid()) {

            $suite->setUseMailConfigExt(false);

            $em->flush();

            return $this->redirect($this->generateUrl('dview_suite_view', array('pid' => $pid, 'sid' => $sid)));
        }
        return $this->render('DviewMailBundle::suite_edit.html.twig', array('form' => $form->createView(), 'content' => $content, 'pid' => $pid, 'sid' => $sid, 'ext' => $suite->getUseMailConfigExt()));
    }

    public function testEditAction(Request $request, $pid, $sid, $tid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);

        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('DviewProjectBundle:Test')->find($tid);
        $mail = $test->getMailConfig();

        if ($request->query->get('ext') == 's') {
            $suite = $em->getRepository('DviewProjectBundle:Suite')->find($sid);
            $test->setMailConfigExt($suite->getMailConfig());
            $test->setUseMailConfigExt(true);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        } else if ($request->query->get('ext') == 'p') {
            $project = $em->getRepository('DviewProjectBundle:Project')->find($pid);
            $test->setMailConfigExt($project->getMailConfig());
            $test->setUseMailConfigExt(true);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        }

        $content = $mail->getAdditionalContent(); // Custom mail body
        $form = $this->createForm(MailType::class, $mail);
        if ($form->handleRequest($request)->isValid()) {

            $test->setUseMailConfigExt(false);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        }
        return $this->render('DviewMailBundle::test_edit.html.twig', array('form' => $form->createView(), 'content' => $content, 'pid' => $pid, 'sid' => $sid, 'tid' => $tid, 'ext' => $test->getUseMailConfigExt()));
    }

}
