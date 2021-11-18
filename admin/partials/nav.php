<?php
    $path_settings = 'admin.php?page=accumulated-points/admin/settings-points.php';
    $path_campaign = 'admin.php?page=accumulated-points/admin/campaign-points.php';
    $path_users = 'admin.php?page=accumulated-points/admin/users-points.php';
    $path_users_history = 'admin.php?page=accumulated-points/admin/history-points.php';
    
?>


<?php echo "<h1 class='wp-heading-inline'>".get_admin_page_title()."</h1>"; ?>
	<?php settings_errors(); ?>
<div>
	<ul class="nav nav-tabs">
    <li ><a href="<?php echo admin_url($path_campaign); ?>">Campaña</a></li>
    <li ><a href="<?php echo admin_url($path_settings); ?>">Ajustes</a></li>
	<li ><a href="<?php echo admin_url($path_users); ?>">Usuarios y puntos</a></li>		
	</ul>

</div>