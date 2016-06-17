<?php

namespace Dview\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DviewUserBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
