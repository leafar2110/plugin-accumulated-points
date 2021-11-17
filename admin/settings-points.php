<?php 
global $wpdb;
global $woocommerce;

$tabla = "{$wpdb->prefix}ap_points_configuration";
$table_user = "{$wpdb->prefix}users";
// Guardar configuracion
if(isset($_POST['btnguardar'])){
    $page = $_POST['page'];
    $status = $_POST['status'];
    $mail = $_POST['mail-value'];
    $message_single = $_POST['message_single'];
    $message_checkout = $_POST['message_checkout'];

    
    $datos = [
        'IdConfig' => null,
        'page_confign' => $page,
        'status_order_confign' => $status,
        'email_confign' => $mail,
        'message_single_confign' => $message_single,
        'message_checkout_confign' => $message_checkout,
    ];
    $respuesta =  $wpdb->insert($tabla,$datos);
}

//actualizar configuracion
if(isset($_POST['btnactualizarconf'])){
    $id = $_POST['IdConfig'];
    $page = $_POST['page'];
    $status = $_POST['status'];
    $mail = $_POST['mail-value'];
    $message_single = $_POST['message_single'];
    $message_checkout = $_POST['message_checkout'];


    $wpdb->update($tabla,
                   array(
                        'IdConfig' => null,
                        'page_confign' => $page,
                        'status_order_confign' => $status,
                        'email_confign' => $mail,
                        'message_single_confign' => $message_single,
                        'message_checkout_confign' => $message_checkout,), 
                   array('IdConfig'=>$id));
}

$query_conf = "SELECT * FROM {$wpdb->prefix}ap_points_configuration";
$list_conf = $wpdb->get_results($query_conf,ARRAY_A);

if(empty($list_conf)){
	$list_conf = array();
    $page_conf = array();
}else{
    $page_conf = $list_conf[0]['page_confign'];
    $status_conf = $list_conf[0]['status_order_confign'];

}


?>

<div class="wrap">
<?php include(AP_POINTS_URL.'admin/partials/nav.php');?>

	<div class="tab-content">
		<div id="tab-3" class="tab-pane active">
			<h3>Configuracion</h3>

            <form class="config_ap" method="post">
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <th>Nombre de uduario</th>
                    <th>Puntos acumulados</th>
                    <th></th>
                    <th></th>
                    
                </thead>
                <tbody id="the-list">
                     
                        <tr>
                            <td>
                            <label for="page">Seleccione page para mostrar regalos</label>
                            </td>
                            <td>  
                            <select name="page" id="type" id="page">

                                <?php $pages = get_pages();?>
                                <?php foreach($pages as $page):?>
                                    <option <?php if($page_conf == $page->post_name){ echo 'selected';}?> value="<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></option>
                                <?php endforeach; ?>
                                </select>
                                     
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <label for=""> Estado del pedido </label>
                            </td>
                            <td>  
                             <?php $status = wc_get_order_statuses();?>
                                <select name="status" id="status" id="status">

                                
                                    <?php foreach($status as $key => $value):?>
                                        <option <?php if($status_conf == $key){ echo 'selected';}?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                    </select>
                                     
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <label for="mail">Correo a notificar canjes</label>
                            </td>
                            <td>  
                             <?php if ($list_conf != true):?>
                                <input type="text" value="" name="mail-value" placeholder="Correo">
                                <?php else: ?>
                                    <input type="text" value="<?php echo $list_conf[0]['email_confign']; ?>" name="mail-value" placeholder="Correo">
                                <?php endif; ?>
                                     
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                        <td>
                        <label for="campaign">Mensaje de ficha del producto</label>
                            </td>
                            <td>  
                            <input type="text" value="<?php echo $list_conf[0]['message_single_confign']; ?>" name="message_single" placeholder="Agregar mensaje">
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                        <td>
                        <label for="campaign">Mensaje del checkout</label>
                            </td>
                            <td>  
                            <input type="text" value="<?php echo $list_conf[0]['message_checkout_confign']; ?>" name="message_checkout" placeholder="Agregar mensaje">

                                     
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                        <td>
                        <?php if ($list_conf != true):?>
                
                        <button type="submit" class="btn-primary_ap" name="btnguardar" id="btnguardar">Guardar</button>
                        <?php else: ?>
                        <input type="hidden" name="IdConfig" value="<?php echo $list_conf[0]['IdConfig']; ?>">
                        <button type="submit" class="btn-primary_ap" name="btnactualizarconf" id="btnactualizarconf">Actualizar</button>
                        <?php endif; ?>
                            </td>
                           
                        </tr>
                        
                        
                </tbody>
            </table>


</form>







		</div>
	</div>
</div>
