<?php

namespace NotesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use NotesBundle\Entity\Nota;
use NotesBundle\Entity\Usuario;



class DefaultController extends Controller
{
    

	 public function loginAction()// este metodo no necesito ningun parametro y objeto creado porque lo unico que necesito es mostrar un twig simplemente, no tengo que pasar aun ningun datos.
    {
    	
        return $this->render('NotesBundle:Default:login.html.twig');
    }


	  public function entrarAction(Request $request)
    {
		
		$nombre = $request->request->get("nombre");// para capturar el valor del formulario por post
    	$password = $request->request->get("password");// para capturar el valor del formulario por post
    	$usuario = $this->getDoctrine()->getRepository("NotesBundle:Usuario")->findOneBy(array("nombre" => $nombre, "password" => $password));//igualo la variable usuario con el getDoctrine y este al repositorio para que me encuentre en la base de datos el nombre y el pass que el usuario le ha puesto por post desde el formulario y que lo mande a principal
    	
    	 if (!$usuario)
    	 {
    	  return $this->render('NotesBundle:Default:login.html.twig'); 
    	 } else { 

    	 	$session = $this->getRequest()->getSession();
    	 	$session->set("id", $usuario->getId());

    	 	 return $this->redirectToRoute('notes_homepage');
    	 }
 	
	}
    
	 public function indexAction()
    {
    	
    	$session = $this->getRequest()->getSession();
		//$session->get("id");(al crear el objeto en el metodo de arriba, $session = $this->getRequest()->getSession(); ya tiene sus metodos los set y los get,  creo  $session->get("id") para que me recoja el id del usuario que ya habiamos puesto en el parametro $session->set("id", $usuario->getId()); )
		$usuario = $this->getDoctrine()->getRepository("NotesBundle:Usuario")->findOneById($session->get("id"));

		 if (!$usuario)
    	 {
    	  return $this->render('NotesBundle:Default:login.html.twig'); 
    	 }

		$notas = $usuario->getNotas();

        return $this->render('NotesBundle:Default:principal.html.twig', array("notas"=>$notas));
    }
	public function crearAction(Request $request)
    {
    	$session = $this->getRequest()->getSession();
    	$usuario = $this->getDoctrine()->getRepository("NotesBundle:Usuario")->findOneById($session->get("id"));
		$titulo = $request->request->get('titulo');// para capturar el valor del formulario por post
		$contenido = $request->request->get('contenido');// para capturar el valor del formulario por post
		$em = $this->getDoctrine()->getManager(); //creo la variable "$em" y la igualo a la variable $this(controlador) y llamo a su metodo doctrine para que me comunique con la base de datos
		$nota = new Nota();//creo la variable nota 
		$nota->setTitulo($titulo);// a esa variable meto la info que quiero darle, en este caso titulo
		$nota->setContenido($contenido);// al igual que anteriormente en este caso contenido
		$nota->setFecha(new \DateTime("now"));// y por último meterle una fecha actual al crear esa nota que queremos
		$nota->setUsuario($usuario);//le doy todo contenido del objeto usuario (en este caso) y relacionarla con ella
		$em->persist($nota); // con el metodo persist puedo pasarle la variable en cuestion en este caso nota y que la meta en la base de datos    
		$em->flush();// y con el metodo flush lo guardo en la base de datos
		

        return $this->redirectToRoute('notes_homepage');
    }

	public function eliminarAction($id)
    {
 		$nota = $this->getDoctrine()->getRepository("NotesBundle:Nota")->findOneById($id);
 		$em = $this->getDoctrine()->getManager();
 		$em->remove($nota);
		$em->flush();
		
		return $this->redirectToRoute('notes_homepage');
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
		
        return $this->redirectToRoute('notes_homepage');
    }






	
}
