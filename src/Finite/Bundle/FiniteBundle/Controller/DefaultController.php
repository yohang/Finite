<?php

namespace Finite\Bundle\FiniteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FiniteFiniteBundle:Default:index.html.twig', array('name' => $name));
    }
}
