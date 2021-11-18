<?php 
global $wpdb;

$tabla = "{$wpdb->prefix}ap_points_history";
$tabla_user = "{$wpdb->prefix}users";




// Actualizar puntos
if(isset($_POST['btn_sumar'])){
    
    $id = stripslashes_deep($_POST['id']); 
    $points = stripslashes_deep($_POST['points']);
    $points_acount = stripslashes_deep($_POST['point_history']);
    $total_points = $points_acount + $points;
    $datos = [
        'IdHistory' => null,
        'ID' => $id,
        'point_history' => $total_points,
        'action_history' => '+'.$points,
        'registered_history' => date("Y:m:d H:i:s"),
    ];
    $respuesta =  $wpdb->insert($tabla,$datos);
}
if(isset($_POST['btn_restar'])){
    $id = stripslashes_deep($_POST['id']); 
    $points = stripslashes_deep($_POST['points']);
    $points_acount = stripslashes_deep($_POST['point_history']);
    $total_points = $points_acount - $points;
    $datos = [
        'IdHistory' => null,
        'ID' => $id,
        'point_history' => $total_points,
        'action_history' => '-'.$points,
        'registered_history' => date("Y:m:d H:i:s"),
    ];
    $respuesta =  $wpdb->insert($tabla,$datos);
}


$query = "SELECT $tabla_user.ID, $tabla_user.display_name, $tabla_user.user_email  FROM $tabla_user";
$list_user = $wpdb->get_results($query,ARRAY_A);




?>

<div class="wrap">
<?php include(AP_POINTS_URL.'admin/partials/nav.php');?>

	<div class="tab-content">
        <div id="tab-1" class="tab-pane active">
        <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <th>Nombre de usuario</th>
                    <th>Puntos acumulados</th>
                    <th></th>
                    
                </thead>
                <tbody id="the-list">
                        <?php foreach ($list_user as $key => $value):?>
                            <?php $id_user = $value['ID'] ?>
                            <?php 
                            $query = "SELECT $tabla.point_history FROM $tabla WHERE ID = $id_user ORDER BY $tabla.registered_history DESC LIMIT 1" ;
                            $list_history = $wpdb->get_results($query,ARRAY_A);
                            ?>
                            
                        <tr>
                            <td><strong class="name_user_ap"><?php echo $value['display_name']; ?></strong><br>
                            <a href="mailto:<?php echo $value['user_email']; ?>" class="btn_email_ap"><?php echo $value['user_email']; ?></a>

                           
                        </td>
                            <td><?php echo $list_history[0]['point_history']; ?> 
                            <button class="btn_history btn_edit_points" id="edit_puntos">Editar Puntos</button>  
                            <div class="form_edit_puntos" style="display: none;">
                                <form id="ap_form" action="" method="post">
                                    <input type="hidden" name="id" value="<?php echo $value['ID']; ?>">
                                    <input type="hidden" name="point_history" value="<?php echo $list_history[0]['point_history']; ?>">
                                        <input type="number" name="points" value="">
                                        <button type="submit" class="btn-primary_ap" name="btn_sumar" id="btnsumar">Sumar</button>
                                        <button type="submit" class="btn-primary_ap" name="btn_restar" id="btnrestar">Restar</button>
                                </form>
                                    
                                </div>
                            </td>
                            <td>
                                <div class="action-btn-ap">
                                <div>
                                <form action="<?php echo admin_url($path_users_history); ?>" method="post">
                                <input type="hidden" name="id" value="<?php echo $value['ID']; ?>">
                                <button class="btn-primary_ap " name="history_points" id="history_points">Historial</button>
                            </form>
                            
                            </div>
                              
                                <div>
                                <form id="ap_form" action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $value['ID']; ?>">
                                        <input type="hidden" name="point_history" value="<?php echo $list_history[0]['point_history']; ?>">
                                         <input type="hidden" name="points" value="<?php echo $list_history[0]['point_history']; ?>">
                                         <button type="submit" class="submitdelete_ap" name="btn_restar" id="btnrestar">Reiniciar puntos</button>
                                    </form>
                                </div>
                                </div>
                           
                              
                            
                            </td>
                        </tr>
                        <?php  endforeach ?>
                </tbody>
            </table>
		</div>
	</div>
</div>

<script>
  jQuery('.btn_edit_points').click(function (){
      jQuery('.form_edit_puntos').removeClass("show-form_edit_puntos")
    jQuery(this).siblings('.form_edit_puntos').addClass("show-form_edit_puntos")
  })


</script>