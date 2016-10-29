<?php

namespace NotesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NotesBundle:Default:index.html.twig');
    }

	public function ayudaAction($tema)
    {
        //return new Response("<html><body>Esta es la ayuda sobre el tema ".$tema."</body></html>");

    	return $this->render("NotesBundle:Default:ayuda.html.twig", array("tema"=> $tema));


    }
}
