<?php

namespace NotesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use NotesBundle\Entity\Nota;
use NotesBundle\Entity\Usuario;



class DefaultController extends Controller
{
    

	 public function loginAction()// este metodo no necesito ningun parametro ni objeto creado porque lo unico que necesito es mostrar un twig simplemente, no tengo que pasar aun ningun dato.
    {
    	$usuario = new Usuario();// Creo la variable obejo ya que luego tengo q la necesito para poder rescatar tu id
    	$session = $this->getRequest()->getSession();// creo la variable sesión y le digo que me captura la sesion que esta abierta
    	$id = $session->get("id", $usuario->getId());// creo la variable id ( le pongo el nombre id para que vaya acorde con lo que deseo pero podria ser cualquier otro nombre) y la igualo al objeto sesion que al crearla anteriormente tiene sus propias funciones en este caso necesito rescatar el id por eso utilizo get que esta compuesto por ("",$) en este caso pongo "id", $usuario->getId() esta ultima variable quiere decir que me coja el id del objeto usuario que esta en la sesión y que lo guarde en la variable id qye posteriormente voy a utilizarla para el if.
    	
    	
    	
    	if($id==null){// si la variable id es nula es decir que no hay sesión, ves al login

    		return $this->render('NotesBundle:Default:login.html.twig');

    	}else{//si no mandame a principal, en el caso de que este logueado y este dentro ya y quiera cambiar la url a login, al estar logueado permanecera en principal
    		 return $this->redirectToRoute('notes_homepage');
    	}
       
    }


	  public function entrarAction(Request $request)
    {
		
		$nombre = $request->request->get("nombre");// para capturar el valor del formulario por post
    	$password = $request->request->get("password");// para capturar el valor del formulario por post
    	$usuario = $this->getDoctrine()->getRepository("NotesBundle:Usuario")->findOneByNombre($nombre);//igualo la variable usuario con el getDoctrine y este al repositorio para que me encuentre en la base de datos el nombre que el usuario le ha puesto por post desde el formulario y que lo mande a principal
        
        if (!$usuario){

           return $this->render('NotesBundle:Default:registrar.html.twig'); 

    	 } 

         $pass = $usuario->getPassword();
         if(password_verify($password, $pass)==false){

            return $this->render('NotesBundle:Default:registrar.html.twig'); 
           
    	 } elseif($usuario || password_verify($password, $pass)){

             $session = $this->getRequest()->getSession(); //establece la sesión
             $session->set("id", $usuario->getId());// y meteme el id del id del usuario en esa sesión y mandame a principal

             return $this->redirectToRoute('notes_homepage');

         }
	}


	 public function cerrarAction()
    {
    	$session = $this->getRequest()->getSession();//este metodo es para crear una sesion y poder llamar a sus funciones
    	$session->clear();//una de las funciones que tienen es clear(); para poder limpiar todos los atributos de la sesión y así poder cerrarla
        return $this->redirectToRoute('notes_login');//retornamos al login
    }

	  public function registrarAction(Request $request)// al metodo lo mandamos por post para q n vea información el usuario
    {
		
		$nombre = $request->request->get("nombre");// para capturar el valor del formulario por post
    	$password = $request->request->get("password");// para capturar el valor del formulario por post
    	$usuario = $this->getDoctrine()->getRepository("NotesBundle:Usuario")->findOneByNombre($nombre);//igualo la variable usuario con el getDoctrine y este al repositorio para que me encuentre en la base de datos el nombre que el usuario le ha puesto por post desde el formulario y que lo mande a principal
    	
    	 if (!$usuario)//si al introducir las credenciales y buscarlas en la base de datos no existe el usuario haz lo siguiente.
    	 {
    	 	$em = $this->getDoctrine()->getManager();// establecemos conexion con la base de datos
    	 	$usuario= new Usuario();//creamos el objeto usuario
    	 	$usuario->setNombre($nombre);//le introducimos el nombre
    	 	$passEncrip = password_hash($password, PASSWORD_DEFAULT);//creo una variable con este nombre y la igualo a la función para encriptar el password 
			$usuario->setPassword($passEncrip);//introducimos el pass mediante las funciones del usuario y entre () el objeto creado con las pass encriptada.
			$em->persist($usuario);//lo introducimos tanto el nombre como el pass que hemos puesto en el objeto usuario al objeto  em ya que el entity manager tiene esta función.
			$em->flush();// finalmente lo guardamos en la base de datos mediante la función flush()
    	 	$session = $this->getRequest()->getSession();//establecemos sesión
    	 	$session->set("id", $usuario->getId());//y le metemos el id a esa sesión el id del usuario


    	 	 return $this->redirectToRoute('notes_login');//llevame al login
    	 
    	 } else { 
    	
    	 	return $this->redirectToRoute('notes_login');//al estar ya registrado lo mandamos otra vez al login.
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
