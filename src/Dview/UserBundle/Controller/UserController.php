<?php

namespace Dview\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller {

    public function indexAction() {
        return $this->render('DviewUserBundle:Default:index.html.twig');
    }

}
