<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * MainController.
 */
class MainController extends AbstractController
{
    /**
     * Action for route: yoda.index.
     */
    public function indexAction(SessionInterface $session): Response
    {

        return new Response("Hello World!");
    }
}