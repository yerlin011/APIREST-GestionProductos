<?php

require_once "vendor/autoload.php";

$app = new \Slim\Slim();

$db = new mysqli("localhost","root","","curso_angular");

// Configuración de cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


//LISTAR TODOS LOS PRODUCTOS
$app->get("/productos",function() use($app,$db){

    $sql = 'SELECT * FROM Productos ORDER BY id DESC';

    $query = $db->query($sql);

    $productos = array();

    while($producto = $query->fetch_assoc()){
       
        $productos[] = $producto;
       
    }
  
    $result = array(
     "status"=>"succes",
     "code"=>200,
     "data"=>$productos

    );

    echo json_encode($result);
});

//CONSEGUIR UN PRODUCTO EN ESPECIFICO
$app->get("/producto/:id",function($id) use($app,$db){

    $sql = 'SELECT * FROM Productos WHERE id ='.$id;

    $query = $db->query($sql);

   $result = array(
        'status'=>'Error',
        'code'=>404,
        'message'=>'Producto no encontrado'
   
       );
       
    if ($query->num_rows ==1){

      $producto = $query->fetch_assoc();

      $result = array(
        'status'=>'succes',
        'code'=>200,
        'data'=>$producto
   
       );
   }

 

    echo json_encode($result);
});

//GUARDAR PRODUCTOS
$app->post("/productos",function() use($app,$db){

    try {
     
        $json = $app->request->post("json");
        $data = json_decode($json,true);
    
        if(!isset($data['nombre'])){
            $data['nombre'] = null;
        }
        
        if(!isset($data['descripcion'])){
            $data['descripcion'] = null;
        }
        
        if(!isset($data['precio'])){
            $data['precio'] = null;
        }
        if(!isset($data['imagen'])){
            $data['imagen'] = null;
        }
    
       $sql = "insert into Productos values(null,".
        "'{$data['nombre']}',".
        "'{$data['descripcion']}',".
        "'{$data['precio']}',".
        "'{$data['imagen']}'".
        ");";
      
        $query = $db->query($sql);
    
        $insert = $query;
    
        $result = array(
            'state'=> 'error',
            'code'=>404,
            'message'=>'No se ingreso el producto'
    
        );
    
        if ($insert){
           
            $result = array(
              'state'=> 'succes',
              'code'=>200,
              'message'=>'Se ingreso el producto correctamente'
    
            );
    
        }
    
        echo json_encode($result);

    } catch (\Throwable $th) {
        throw $th;
    }
   
});
//ELIMINAR UN PRODUCTO
$app->get("/delete-producto/:id", function($id) use( $db,$app){

    $sql = 'DELETE FROM Productos WHERE id='.$id;

    $query = $db->query($sql);
 
    if ($query){
      
     $result = array(
      'status'=>'succes',
      'code'=>'200',
      'message'=>'El producto se ha eliminado correctamente'
     );
 
    }else{
        $result = array(
        'status'=>'error',
        'code'=>'404',
        'message'=>'El producto no se ha eliminado'
        );
 
 }
 
 echo json_encode($result);
 
 
 
 });

//ACTUALLIZAR UN PRODUCTO

$app->post("/update-producto/:id",function($id) use($db,$app){

  $json = $app->request->post("json");
  $data = json_decode($json,true);

  $sql = "UPDATE Productos SET ".
         "nombre = '{$data["nombre"]}',".
         "descripcion = '{$data["descripcion"]}',";

    if (isset($data["imagen"])){

        $sql .=  "imagen = '{$data["imagen"]}',";
    }
   
    $sql .=     "precio = '{$data["precio"]}' WHERE id = {$id}";

   

    $query = $db->query($sql);

    if ($query){

        $result = array(
         'status'=>'succes',
         'code'=>'200',
         'message'=>'El producto se ha actualizado correctamente!!'

        );

    }else{
        $result = array(
            'status'=>'error',
            'code'=>'404',
            'message'=>'El producto no se actualizo!!'
   
           );

    }

    echo var_dump($sql);

    echo json_encode($result);
});

//SUBIR UNA IMAGEN A UN PRODUCTO

$app->post("/upload-file", function() use($db,$app){

    $result = array(
        'status'=>'error',
        'code'=>'404',
        'message'=>'El archivo no ha podido subirse'

       );

    if (isset($_FILES["uploads"])){

       $PiramideUploader = new PiramideUploader();

       $upload =  $PiramideUploader->upload("image","uploads","uploads",array('image/jpeg','image/png','image/gif'));
       $file = $PiramideUploader->getInfoFile();
       $file_name = $file["complete_name"];

     if (isset($upload) && $upload["uploaded"]==false){

         $result = array(
            'status'=>'error',
            'code'=>'404',
            'message'=>'El archivo no ha podido subirse'
    
           );

       }
       else{

        $result = array(
            'status'=>'succes',
            'code'=>'200',
            'message'=>'El archivo se ha subido correctamente!!',
            'file'=>$file_name
    
           );
       }
    
    }

    echo  json_encode($result);

});


$app->run();



?>

