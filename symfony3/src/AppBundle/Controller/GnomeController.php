<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GnomeController extends FOSRestController {

    /**
     * @Rest\Get("/api/test")    
     * 
     * @return mixed
     */
    public function testAction() {

        return new View("OK", Response::HTTP_OK);
    }

}
