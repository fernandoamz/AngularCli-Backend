<?php 

require_once 'vendor/autoload.php';

$app = new \Slim\Slim(); 
$db = new mysqli('localhost', 'root', '','curso_angular4');


$app->get("/pruebas", function() use($app,$db){
    echo " Holaaaa Mundo desde Slim PHP ";
    var_dump($db);
});

$app->get("/probando", function() use($app){
    echo "otro texto  ";
});

//LISTAR TODOS LOS PRODUCTOS

$app->get('/productos', function() use($db, $app){

    $sql = 'SELECT * FROM productos ORDER BY id DESC;';
    $query = $db->query($sql);

    //var_dump($query->fetch_assoc());
    $productos = array();
    while($producto = $query-> fetch_assoc()){
        $productos[] = $producto;
    }

    $result = array(
        'status' => 'success',
        'code' => 200,
        'data' => $productos
    );

    echo json_encode($result);
});

//DEVOLVER UN SOLO PRODUCTO 

$app->get('/productos/:id', function($id) use($db, $app){

    $sql = 'SELECT * FROM productos WHERE id = '.$id;
    $query = $db->query($sql);

    $result = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'Producto no disponible'
    );

    if($query->num_rows == 1){
         $producto = $query->fetch_assoc(); 
         
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => $producto
        );
    }
    
    echo json_encode($result);

});

// ELIMINAR UN PRODUCTO

$app->get('/eliminar-productos/:id', function($id) use($db, $app){

    $sql = 'DELETE FROM productos WHERE id = '.$id;

    $query = $db->query($sql);

    if($query){         
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'El producto se ah eliminado correctamente !!'
        );
    }else{
         $result = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'El producto NO se ah eliminado correctamente !!'
          );
    }

    echo json_encode($result);
});
// ACTUALIZAR UN PRODUCTO 
$app->post('/actualizar-productos/:id', function($id) use($db, $app){
    $json = $app->request->post('json');
    $data = json_decode($json,true);

    $sql = " UPDATE productos ".
           " SET nombre = '{$data["nombre"]}', ".
           "     description = '{$data["description"]}', ";

           if(isset($data['imagen'])){
               $sql .= "imagen = '{$data["imagen"]}', ";
           }

           " precio = '{$data["precio"]}'  WHERE id = {$id}";		

    $query = $db->query($sql);

    if($query){         
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'El producto se ah actualizado correctamente !!'
        );
    }else{
         $result = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'El producto NO se ah actualizado correctamente !!'
          );
    }

    echo json_encode($result);
});

// SUBIR UNA IMAGEN A UN PRODUCTO 
$app->post('/upload-file', function() use($db,$app){
    $result = array(
        'status' => 'error',
        'code' => 404,
        'message' => 'el archivo no se ah podido subir !!'
    );

    if(isset($_FILES['uploads'])){
       $piramideUploader = new PiramideUploader();
       
       $upload = $piramideUploader->upload("image","uploads","uploads", array('image/jpeg','image/png','image/gif'));
       $file = $piramideUploader->getInfoFile();
       $file_name = $file['complete_name'];

       if(isset($upload) && $upload["uploaded"] == false){
         $result = array(
         'status' => 'error',
         'code' => 404,
         'message' => 'el archivo no se ah podido subir !!'
         );
       }else{
         $result = array(
         'status' => 'error',
         'code' => 404,
         'message' => 'el archivo se ah subido !!',
         'filename' => $file_name
         );
       }
    }

});

// GUARDAR PRODUCTOS
$app->post('/productos', function() use($app, $db){

    $json = $app->request->post('json');
    $data = json_decode($json, true);

    //var_dump($json);
    //var_dump($data);

   if(!isset($data['nombre'])){
       $data['nombre'] = null;
    }

    if(!isset($data['description'])){
       $data['description'] = null;
    }

    if(!isset($data['precio'])){
        $data['precio'] = null;
        }

    if(!isset($data['imagen'])){
        $data['imagen'] = null;
        }


    $query="INSERT INTO productos VALUES(NULL,".
            "'{$data['nombre']}',".
            "'{$data['description']}',".
            "'{$data['precio']}',".
            "'{$data['imagen']}'".
            ");";
        print $query;
        $insert = $db->query($query);
        print $insert;
        $result = array(
            'status' => 'error',
            'code' => 400,
            'message' => 'Producto NO creado correctamente'
        );

        if($insert){
            $result = array(
                'status' => 'exito',
                'code' => 200,
                'message' => 'Producto creado correctamente'
            );
        }
        
        echo json_encode($result);

    });
//este objeto corre todas las rutas anteriores
$app->run();
?>