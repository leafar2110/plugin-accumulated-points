<?php 
global $wpdb;

$tabla = "{$wpdb->prefix}ap_points_history";
$tabla_user = "{$wpdb->prefix}users";

// Actualizar puntos
if(isset($_POST['history_points'])){
    $id = $_POST['id'];
    $query = "SELECT $tabla_user.display_name, $tabla.registered_history,  $tabla.action_history, $tabla.point_history FROM $tabla_user LEFT JOIN $tabla on $tabla_user.ID = $tabla.ID WHERE $tabla.ID = $id ORDER BY $tabla.registered_history DESC LIMIT 15";
    $list_user = $wpdb->get_results($query,ARRAY_A);
}
if(empty($list_user)){
    $list_user = array();
}

?>

<div class="wrap">
<?php include(AP_POINTS_URL.'admin/partials/nav.php');?>

	<div class="tab-content">
        <div id="tab-1" class="tab-pane active">
        <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <th>Nombre de usuario</th>
                    <th>Fecha</th>
                    <th>Historial</th>
                    <th>Total</th>
                    
                </thead>
                <tbody id="the-list">
                        <?php foreach ($list_user as $key => $value):
                            $date = date_create($value['registered_history']);?>
                        <tr>
                            <td><?php echo $value['display_name']; ?></td>
                            <td><?php echo date_format($date, 'd/m/Y'); ?></td>
                            <td><?php echo $value['action_history']; ?>
                            <td><?php echo $value['point_history']; ?>
                               
                            </td>
                           
                        </tr>
                        <?php endforeach ?>
                </tbody>
            </table>
		</div>
	</div>
</div>
