<?php

namespace NotesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use NotesBundle\Entity\Nota;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$notas= $this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();
		
      return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }
public function crearAction($titulo,$contenido)
    {
 		$em = $this->getDoctrine()->getManager();
		$nota= new Nota();
		$nota->setTitulo($titulo);
		$nota->setContenido($contenido);
		$nota->setFecha(new \DateTime("now"));
		$em->persist($nota);     
		$em->flush();
		$notas= $this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();

      return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }

public function eliminarAction($id)
    {
 		$nota= $this->getDoctrine()->getRepository("NotesBundle:Nota")->findOneById($id);
 		$em = $this->getDoctrine()->getManager();
 		$em->remove($nota);
		$em->flush();
		$notas= $this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();

      return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }

public function actualizarAction($id,$titulo,$contenido)
    {
 		$em = $this->getDoctrine()->getManager();
 		$nota= $this->getDoctrine()->getRepository("NotesBundle:Nota")->findOneById($id);
		$nota->setTitulo($titulo);
		$nota->setContenido($contenido);
		$nota->setFecha(new \DateTime("now"));
		$em->flush();
		$notas= $this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();

      return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }

	
}
