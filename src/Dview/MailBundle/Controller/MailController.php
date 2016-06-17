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

        $test = $this->getDoctrine()->getManager()->getRepository('DviewProjectBundle:Test')->getTest($params->_test);
        if ($test == NULL) {
            throw new HttpException(404, "Test wasn't found");
        }

        $config = $test->getMailConfig();
        if ($config == NULL) {
            $config = $test->getMailConfigExt();
            if ($config == NULL) {
                throw new HttpException(500, 'Mail service not configured for this test');
            }
        }

        $suite = $test->getSuite();
        $project = $suite->getProject();

        if ($config->getWithAdditionalContent()) {
            $additionalContent = $config->getAdditionalContent();
        } else {
            $additionalContent = NULL;
        }
        
        if($params->status === 2){
            $params->status = 'OK';
        } else if($params->status === 3){
            $params->status = 'KO';
        } else if($params->status === 4){
            $params->status = 'N/A';
        }

        /*
         * Put values of 'mail variables' in the 'additional content' mail section
         */
        $additionalContent = preg_replace(
                array('#\$status#', '#\$step#', '#\$percentage#', '#\$date#', '#\$time#',
                    '#\$browser#', '#\$version#', '#\$os#', '#\$project#', '#\$suite#', '#\$name#'),
                array($params->status, $params->comparison->step, $params->misMatchPercentage,
                    date('d/m/Y H:i:s', $params->initDate), $params->runtime, $params->browser->name,
                    $params->browser->version, $params->browser->platform,
                    $project->getName(), $suite->getName(), $test->getName()
                ), $additionalContent);

        if ($params->status === 2 && $config->getOnOK() || $params->status != 2 && $config->getOnKO()) {
            $message = \Swift_Message::newInstance()
                    ->setSubject("[Dview] Rapport d'exécution du test " . $test->getName())
                    ->setFrom('noreply@dview.com')
                    ->setBody(
                    $this->renderView(
                            'DviewMailBundle:Mail:result.html.twig', array('result' => $params, 'test' => $test, 'suite' => $suite, 'project' => $project, 'additionalContent' => $additionalContent, 'withStatus' => $config->getWithStatus(), 'withInfo' => $config->getWithInfo())
                    ), 'text/html'
                    )
            ;

            if ($config->getCustomObject() != NULL) {
                $message->setSubject($config->getCustomObject());
            }

            foreach ($config->getReceivers() as $receiver) {
                $message->setTo($receiver->getEmail());
                $this->get('mailer')->send($message);
            }
        } else {
            throw new HttpException(204, 'Mail not desired');
        }

        return new Response();
    }

    /*
     * Curl cmd line to test the feature
     */

//    curl -X POST -d '{"_id":"574ce33cb7789ed86a13ff37","_test":"574c028ee90c190d613e4981","misMatchPercentage":0,"startDate":1464656714555,"initDate":1464656700751,"__v":0,"browser":{"name":"firefox","version":"46.0.1","platform":"LINUX"},"status":2,"runtime":0}' dview.local/app_dev.php/mail

    public function testEditAction(Request $request, $pid, $sid, $tid) {
        $this->get('tool_controller')->currentUserCanAccess($pid, false);

        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('DviewProjectBundle:Test')->find($tid);
        $mail = $test->getMailConfig();
        if ($mail == null) {
            $mail = new \Dview\MailBundle\Entity\MailConfig;
            $test->setMailConfig($mail);
        }
        $content = $mail->getAdditionalContent();
        $form = $this->createForm(MailType::class, $mail);
        if ($form->handleRequest($request)->isValid()) {
            $em->persist($mail);
            $em->flush();

            return $this->redirect($this->generateUrl('dview_test_view', array('pid' => $pid, 'sid' => $sid, 'tid' => $tid)));
        }
        return $this->render('DviewMailBundle::edit.html.twig', array('form' => $form->createView(), 'content' => $content, 'pid' => $pid, 'sid' => $sid, 'tid' => $tid));
    }

}
