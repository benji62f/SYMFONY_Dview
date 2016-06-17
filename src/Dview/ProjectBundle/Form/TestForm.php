<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dview\ProjectBundle\Form;

class TestForm {

    private $name;
    private $browser;
    private $width;
    private $height;
    private $page;
    private $actions;
    private $schedule;
    private $threshold;
    private $zone;

    function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->browser = $name;
    }

    function getBrowser() {
        return $this->browser;
    }

    function getWidth() {
        return $this->width;
    }

    function getHeight() {
        return $this->height;
    }

    function getPage() {
        return $this->page;
    }

    function getActions() {
        return $this->actions;
    }

    function getZone() {
        return $this->zone;
    }

    function setBrowser($browser) {
        $this->browser = $browser;
    }

    function setWidth($width) {
        $this->width = $width;
    }

    function setHeight($height) {
        $this->height = $height;
    }

    function setPage($page) {
        $this->page = $page;
    }

    function setActions($actions) {
        $this->actions = $actions;
    }

    function setZone($zone) {
        $this->zone = $zone;
    }

    function getSchedule() {
        return $this->schedule;
    }

    function setSchedule($schedule) {
        $this->schedule = $schedule;
    }

    function getThreshold() {
        return $this->threshold;
    }

    function setThreshold($threshold) {
        $this->threshold = $threshold;
    }

}
