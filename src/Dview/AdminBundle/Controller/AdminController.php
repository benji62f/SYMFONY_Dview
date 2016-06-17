<?php

namespace Dview\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller {

    public function indexAction() {
        return $this->render('DviewAdminBundle:Admin:index.html.twig');
    }

}
