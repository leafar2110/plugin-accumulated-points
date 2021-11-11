<?php 
global $wpdb;

$tabla = "{$wpdb->prefix}users";

// Actualizar puntos
if(isset($_POST['btnactualizar'])){
    $id = stripslashes_deep($_POST['id']); 
    $points = stripslashes_deep($_POST['points']);

    $wpdb->update($tabla, array('ID'=>$id, 'user_points'=>$points), array('ID'=>$id));
}
$query = "SELECT * FROM {$wpdb->prefix}users";
$list_user = $wpdb->get_results($query,ARRAY_A);
if(empty($list_user)){
	$list_user = array();
}

$path = 'admin.php?page=accumulated-points/admin/settings-points.php';
$cpt = 'edit.php?post_type=ap_regalo';
?>

<div class="wrap">
<?php echo "<h1 class='wp-heading-inline'>".get_admin_page_title()."</h1>"; ?>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
    <li ><a href="<?php echo admin_url($cpt); ?>">Regalos</a></li>
    <li ><a href="<?php echo admin_url($path); ?>">Ajustes</a></li>
	<li class="active" ><a href="#>">Usuarios y puntos</a></li>	
	</ul>

	<div class="tab-content">
        <div id="tab-1" class="tab-pane active">
        <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <th>Nombre de uduario</th>
                    <th>Puntos acumulados</th>
                    <th>Accion</th>
                    
                </thead>
                <tbody id="the-list">
                        <?php foreach ($list_user as $key => $value):?>
                        <tr>
                            <td><?php echo $value['display_name']; ?></td>
                            <td><?php echo $value['user_points']; ?>
                                <div>
                                    <form id="ap_form" action="" method="post">
                                        <input type="hidden" name="id" value="<?php echo $value['ID']; ?>">
                                         <input type="number" name="points" value="<?php echo $value['user_points']; ?>">
                                         <button type="submit" class="page-title-action" name="btnactualizar" id="btnguardar">Actualizar</button>
                                    </form>
                                    
                                </div>
                            </td>
                            <td>
                                <button class="page-title-action">Editar Puntos</button>
                            </td>
                        </tr>
                        <?php endforeach ?>
                </tbody>
            </table>
		</div>
	</div>
</div>
