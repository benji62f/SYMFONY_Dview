<?php

namespace Dview\ToolBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ToolController extends Controller {

    /**
     * Checks current user rights on the provided project.
     * 
     * @param type $pid
     * @param type $readOnly
     * @return boolean
     */
    public function currentUserCanAccess($pid, $readOnly) {
        $project = $this->getDoctrine()->getManager()->getRepository('DviewProjectBundle:Project')->find($pid);

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER') && $project->getManager()->getId() == $this->getUser()->getId()) {
                return; // if i'm a manager and $pid is my project
            }
            if ($readOnly && $this->get('security.authorization_checker')->isGranted('ROLE_VIEWER') && $project->getClient()->getId() == $this->getUser()->getId()) {
                return; // if i'm a client and $pid is my project AND i just want view something (no adding / editing / deleting something)
            }
            throw new AccessDeniedException();
        }
        return; // if i'm admin, i've every rights ! :D
    }
}
