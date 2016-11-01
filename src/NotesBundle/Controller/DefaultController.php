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
    	//$request->request->get('request');
		$notas = $this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();// me lista las notas que tengo
		
        return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }
public function crearAction(Request $request)
    {
		$titulo = $request->request->get('titulo');// para capturar el valor del formulario por post
		$contenido = $request->request->get('contenido');// para capturar el valor del formulario por post
		$em = $this->getDoctrine()->getManager(); //creo la variable "$em" y la igualo a la variable $this(controlador) y llamo a su metodo doctrine para que me comunique con la base de datos
		$nota = new Nota();//creo la variable nota 
		$nota->setTitulo($titulo);// a esa variable meto la info que quiero darle, en este caso titulo
		$nota->setContenido($contenido);// al igual que anteriormente en este caso contenido
		$nota->setFecha(new \DateTime("now"));// y por último meterle una fecha actual al crear esa nota que queremos
		$em->persist($nota); // con el metodo persist puedo pasarle la variable en cuestion en este caso nota y que la meta en la base de datos    
		$em->flush();// y con el metodo flush lo guardo en la base de datos
		$notas=$this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();// me lista las notas que vuelvo a tener

        return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }

public function eliminarAction($id)
    {
 		$nota = $this->getDoctrine()->getRepository("NotesBundle:Nota")->findOneById($id);
 		$em = $this->getDoctrine()->getManager();
 		$em->remove($nota);
		$em->flush();
		$notas=$this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();

        return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }

public function actualizarAction($id)
  {
    	$em = $this->getDoctrine()->getManager();
    	$nota = $this->getDoctrine()->getRepository("NotesBundle:Nota")->findOneById($id);
		
		return $this->render('NotesBundle:Default:editar.html.twig', array("nota"=>$nota));
}
public function editarAction(Request $request)
    {
 		$id = $request->request->get('id');// para capturar el valor del formulario por post
 		$titulo = $request->request->get('titulo');// para capturar el valor del formulario por post
		$contenido = $request->request->get('contenido');// para capturar el valor del formulario por post
 		$em = $this->getDoctrine()->getManager();
 		$nota = $this->getDoctrine()->getRepository("NotesBundle:Nota")->findOneById($id);
		$nota->setTitulo($titulo);
		$nota->setContenido($contenido);
		$nota->setFecha(new \DateTime("now"));
		$em->flush();
		$notas=$this->getDoctrine()->getRepository("NotesBundle:Nota")->findAll();

        return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }

	
}
