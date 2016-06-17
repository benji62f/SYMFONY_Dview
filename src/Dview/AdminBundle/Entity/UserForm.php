<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dview\AdminBundle\Entity;

class UserForm {

    private $username;
    private $email;
    private $password;
    private $role;

    public function __construct(\Dview\UserBundle\Entity\User $user) {
        $this->username = $user->getUsername();
        $this->email = $user->getEmail();
        
        if($user->hasRole('ROLE_ADMIN'))
            $this->role = 'ROLE_ADMIN';
        else if($user->hasRole('ROLE_MANAGER'))
            $this->role = 'ROLE_MANAGER';
        else if($user->hasRole('ROLE_VIEWER'))
            $this->role = 'ROLE_VIEWER';
    }

    function getUsername() {
        return $this->username;
    }

    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }

    function getRole() {
        return $this->role;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setRole($role) {
        $this->role = $role;
    }


}
