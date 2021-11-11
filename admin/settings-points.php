<?php 
global $wpdb;

$tabla = "{$wpdb->prefix}ap_points_configuration";

// Guardar configuracion
if(isset($_POST['btnguardar'])){
    $page = $_POST['page'];
    $category = $_POST['texto'];
    $array = json_encode($category);
    $points = $_POST['point-value'];
    $euros = $_POST['euro-value'];
    $mail = $_POST['mail-value'];
    $date_init = $_POST['date_init'];
    $date_fin = $_POST['date_fin'];
    
    $datos = [
        'IdConfig' => null,
        'page' => $page,
        'category' => $array,
        'email' => $mail,
        'points' => $points,
        'euros' => $euros,
        'fecha_inicio' => $date_init,
        'fecha_final' => $date_fin,
        'stado' => true,
    ];
    $respuesta =  $wpdb->insert($tabla,$datos);
}

//actualizar configuracion
if(isset($_POST['btnactualizarconf'])){
    $id = $_POST['IdConfig'];
    $page = $_POST['page'];
    $category =  $_POST['texto'];
    $array = json_encode($category);
    $points = $_POST['point-value'];
    $euros = $_POST['euro-value'];
    $email = $_POST['mail-value'];
    $date_init = $_POST['date_init'];
    $date_fin = $_POST['date_fin'];
    

    $wpdb->update($tabla, array('IdConfig'=>$id, 'page'=>$page, 'category'=>$array, 'email'=>$email, 'points'=>$points, 'euros'=>$euros, 'fecha_inicio' => $date_init, 'fecha_final' => $date_fin, 'stado' => true, ), array('IdConfig'=>$id));
}

$query_conf = "SELECT * FROM {$wpdb->prefix}ap_points_configuration";
$list_conf = $wpdb->get_results($query_conf,ARRAY_A);

if(empty($list_conf)){
	$list_conf = array();
    $page_conf = array();
}else{
    $page_conf = $list_conf[0]['page'];
}

$path = 'admin.php?page=accumulated-points/admin/users-points.php';
$cpt = 'edit.php?post_type=ap_regalo';
?>

<div class="wrap">
<?php echo "<h1 class='wp-heading-inline'>".get_admin_page_title()."</h1>"; ?>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
    <li ><a href="<?php echo admin_url($cpt); ?>">Regalos</a></li>
    <li class="active"><a href="#">Ajustes</a></li>
	<li ><a href="<?php echo admin_url($path); ?>">Usuarios y puntos</a></li>	
	</ul>

</div>
	<div class="tab-content">
		<div id="tab-3" class="tab-pane active">
			<h3>Configuracion</h3>
            <form method="post">
                <div>
                <?php $pages = get_pages();?>
                    <label for="page">Seleccione page para mostrar regalos</label><br>
                    <select name="page" id="type" id="page">

                        <?php $pages = get_pages();?>
                        <?php foreach($pages as $page):?>
                            <option <?php if($page_conf == $page->post_name){ echo 'selected';}?> value="<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div><br>
                <div>
                <?php $pages = get_pages();?>
                    <label for="page">Categoria de productos</label><br>
                 
                        <?php   $product_terms = get_terms( 'product_cat' );
                            foreach($product_terms as $product_term):?>
                            <div>
                            <input type="checkbox" name="texto[]"  value="<?php echo $product_term->name; ?>">
                            <label for=""><?php echo $product_term->name; ?></label>
                            </div>
                        <?php endforeach; ?>
                   
                </div><br>
                <div>
                    <label for="point">Puntos por Euros gastados</label><br>
                    <input type="number" value="<?php echo $list_conf[0]['points']; ?>" name="point-value" placeholder="Puntos"> X <input type="number" value="<?php echo $list_conf[0]['euros']; ?>" name="euro-value" placeholder="Euros">

                </div><br>
                <div>
                    <label for="fecha">Fecha de inicio</label><br>
                    <input type="date" value="<?php echo $list_conf[0]['fecha_inicio']; ?>" name="date_init" placeholder="Puntos"> X <input type="date" value="<?php echo $list_conf[0]['fecha_final']; ?>" name="date_fin" placeholder="Euros">

                </div><br>
                <div>
                    <label for="mail">Correo a notificar canjes</label><br>
                    <?php if ($list_conf != true):?>
                    <input type="text" value="" name="mail-value" placeholder="Correo">
                    <?php else: ?>
                        <input type="text" value="<?php echo $list_conf[0]['email']; ?>" name="mail-value" placeholder="Correo">
                    <?php endif; ?>
                </div><br>
                <div>
                <?php if ($list_conf != true):?>
                
                <button type="submit" class="page-title-action" name="btnguardar" id="btnguardar">Guardar</button>
                <?php else: ?>
                <input type="hidden" name="IdConfig" value="<?php echo $list_conf[0]['IdConfig']; ?>">
                <button type="submit" class="page-title-action" name="btnactualizarconf" id="btnactualizarconf">actualizar</button>
                <?php endif; ?>
                </div>
            </form>


		</div>
	</div>
</div>
