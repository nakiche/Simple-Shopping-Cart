<?php
	
	session_start();

	$current_status=($_SESSION['current_cart']);

	require 'conexion.php';


	if (isset($_SESSION['carrito'])) /*Valido si existe una sesion*/
	{
		if(!empty($_GET["action"]))  /*valido si me estan mandando un action*/
		{
			switch($_GET["action"]) 
			{

			case "remove":
			
				
				$arreglo=$_SESSION['carrito'];
			
				for($c=0;$c<count($arreglo);$c++)
				{
					if($arreglo[$c]['Id']==$_GET['code'])
					{

					unset($_SESSION['carrito'][$c]);
					$_SESSION['carrito']=array_values($_SESSION['carrito']);

					//print_r(array_values($_SESSION['carrito']));

						if (empty($_SESSION['carrito']))
						{
							unset($_SESSION['carrito']);
						}

					}


				}
					
			break;
		
			case "empty":

			unset($_SESSION['carrito']);


			break;	

	   }
	}	





	   if(isset($_GET['id'])) /*Valido si me están pasando un id*/
	   {	
	   		
			$arreglo=$_SESSION['carrito']; /*almaceno la sesion en un array*/
			$encontro=false;
			$numero=0;




				for($i=0;$i<count($arreglo);$i++) /*Recorro el array*/
				{
					if($arreglo[$i]['Id']==$_GET['id'])   /*Valido si hay un id de producto en el arreglo igual al que me estan pasando para sumarle uno mas*/
					{
						$encontro=true;
						$numero=$i;
					}
				}
				
				if ($encontro==true) /*si ya existe un producto en el arreglo le agrego otro y actualizo la sesion, agrego cantidad al array*/
				{
					
					$arreglo[$numero]['Cantidad']=$arreglo[$numero]['Cantidad']+($_GET['quantity']);
					$_SESSION['carrito']=$arreglo;
				
				}else{
				
					$nombre=""; /*Inicio variables de texto y numericas y me conecto con la base de datos con el id que me pasan*/
					$precio=0;
					$imagen="";
					$conexion=mysqli_connect($db_host,$db_usuario,$db_contra);
					//si llega a ejecutarse esta funcion
					if(mysqli_connect_errno())
					{
						echo "Fallo al conectar con la base de datos";
						exit();		
					}
					mysqli_select_db($conexion,$db_nombre)or die("no se encuentra la bdD");
					//para incluir los tildes	
					mysqli_set_charset($conexion, "utf8");
					$consulta="SELECT * FROM productos WHERE ID=".$_GET['id']." ";
					$resulados= mysqli_query($conexion,$consulta);
			
					while($fila=mysqli_fetch_array($resulados, MYSQL_ASSOC)) 
					{
						$nombre=$fila['nombre']; /*cargo en unas variables lo que hay en la base de datos del producto*/
						$precio=$fila['precio'];
						$imagen=$fila['imagen'];
					}
				
					$datosnuevos=array('Id'=>$_GET['id'], /*Cargamos un array con los valores del producto que acabamos de pasar, incluyendo cantidad*/
					'Nombre'=>$nombre,
					'Precio'=>$precio,
					'Imagen'=>$imagen,
					'Cantidad'=>($_GET['quantity']));	
			
					array_push($arreglo,$datosnuevos); /*le añadimos al array que ya existia, nuevos registros una cola a la pila*/
					$_SESSION['carrito']=$arreglo; /*cargamos la sesion con el array actualizado*/	

					
				
				}
	   }
		
	}else{  /*si no hay una sesion carrito previa*/
		

		if(isset($_GET['id'])) /*Valido si me están pasando un id*/
		{

			$nombre="";
			$precio=0;
			$imagen="";
			$conexion=mysqli_connect($db_host,$db_usuario,$db_contra);
			//si llega a ejecutarse esta funcion
			if(mysqli_connect_errno())
			{
				echo "Fallo al conectar con la base de datos";
				exit();		
			}
			mysqli_select_db($conexion,$db_nombre)or die("no se encuentra la bdD");
			//para incluir los tildes	
			mysqli_set_charset($conexion, "utf8");
			$consulta="SELECT * FROM productos WHERE ID=".$_GET['id']." ";
			$resulados= mysqli_query($conexion,$consulta);
			
			while($fila=mysqli_fetch_array($resulados, MYSQL_ASSOC)) 
			{
				$nombre=$fila['nombre'];
				$precio=$fila['precio'];
				$imagen=$fila['imagen'];
			}
				
			$arreglo[]=array('Id'=>$_GET['id'],   /*creo un arreglo porque es la primera sesion y almaceno los datos por primera vez en 'carrito'*/
			'Nombre'=>$nombre,
			'Precio'=>$precio,
			'Imagen'=>$imagen,
			'Cantidad'=>($_GET['quantity']));	
			 $_SESSION['carrito']=$arreglo;
		}
	}

?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<title>SHOPPING CART</title>
<link rel="stylesheet" type="text/css" href="styles.css">
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="./js/scripts.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<link href='images/favicon.ico' rel='shortcut icon' type='image/png'>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>
<div class="contenedor">

	<div class="block_header">
       <div class="header">
    		 
        	 <div class="logo">
              <a href="./index.php"><img src="images/sc.png" width="265" height="78" 	border="0" alt="logo"></a>
        	 </div>
    
			           
        <div class="clr"></div>
  		</div>  
    <div class="clr"></div>
    </div>   
             
	<div class="FBG">
    	<div class="FBG_content"> 
        
          <div class="products_listing">	
        		
    			<h3>Shipping and payment</h3> 		

    			<?php

    			if(isset($_SESSION['carrito'])) /*Valido si existe una sesion previa*/
				{

				?>	
    		
	    		<h2><a href="index.php"> Search products </a></h2>
    			<?php

    			}

    			?>
	
	<?php
		
		$current=$_SESSION['current_cash'];
    	$total=0;
    	$result="test";
    	/*for current status*/
    	$current_status=0;

		if(isset($_SESSION['carrito'])) /*Valido si existe una sesion previa*/
		{
			$datos=$_SESSION['carrito']; /*almaceno los datos de la sesion en un array*/
			//var_dump($datos);

			
			for($i=0;$i<count($datos);$i++)  /*Cargo todos los datos que tengo en el carrito y multiplico la cantidad por los precios para subtotalizar*/
			{
	?>		

		
			<div class="producto">
            	<div class="centrado">

            		<img src="./images/<?php echo $datos[$i]['Imagen']; ?>"><br>   
                    <span><?php echo $datos[$i]['Nombre'];?></span><br>
                    <span>Price: <?php echo  '$ ' .$datos[$i]['Precio'].'';?></span><br>
                    <span>Quantity: 
                    <input type="text" value="<?php echo $datos[$i]['Cantidad'];?>"
                    data-id="<?php echo $datos[$i]['Id'];?>"
                    data-precio="<?php echo $datos[$i]['Precio'];?>"
                    class="cantidad" onkeypress="return isNumberKey(event);" onkeyup="validacion();"></span><br>
                    <span class="subtotal">Subtotal:<?php echo ' $' . $datos[$i]['Cantidad']*$datos[$i]['Precio'].' ';?></span><br>
                    <input type="submit" class="button" value="Remove Item" onclick="document.location.href='./carritodecompras.php?action=remove&code=<?php echo $datos[$i]['Id']; ?>'"><br>
                    
                    <span class="error"></span>

				</div>
        	</div>
	
			<?php  
		
			$total=($datos[$i]['Cantidad']*$datos[$i]['Precio'])+$total;    	
			
			/*total items*/
			$current_status= $current_status+$datos[$i]['Cantidad'];		
                        
            $_SESSION['current_cart'] = $current_status;        

			}


			?>
		  </div>
			<!-- Mensaje de error -->	
			<div class="error2">
			<h2 class="errortext"></h2>			
			</div>

			<?php 

			/*Current car status*/
			echo '<h2 id="total">Items in your cart: ' .$current_status .' / Subtotal: $' .number_format($total,2). '</h2>';
			echo '<h2>Your Current Cash is: $'.number_format($current,2). '</h2>';
			echo '<p><a href="./carritodecompras.php?action=empty">Remove cart</a></p>';
			}
			else
			{	
			
			echo '<h2 id="total">Items in your cart: ' .$current_status .' / Subtotal: $' .number_format($total,2). '</h2>';
			echo '<h2>Your Current Cash is: $'.number_format($current,2). '</h2>';
			echo '<h2><a href="index.php"> Search products </a></h2>';
			}			

			?>
		
		<?php 
		
		if(isset($_SESSION['carrito'])) /*Valido si existe una sesion previa*/
		{	
       		if (!empty($datos))
       		{	
     
       	?>
       		<div class="centrado">
       		  <form action="shipoptionselect.php" name="shipping" method="get" onsubmit="return validarSelect(this);" onchange="validarSelect2();" >	
       			<select id="opciones" name="pick" class="select">
				 <option value="" selected="">Select Transport type</option>
  				 <option value="pickup" id="Pick">Pick up - Free</option>
 				 <option value="ups" id="UPS">UPS - 5$</option>
  				</select>
  				<input type="submit" class="button2" value="Pay!" onclick="return validacion();" >
  			  </form>
			</div>

		<?php

			}
		}
		
		?>

		</div>
	</div>

</div>
            
</body>
</html>