<?php 
global $wpdb;

$tabla_campaign = "{$wpdb->prefix}ap_points_campaign";
// Guardar configuracion
if(isset($_POST['btnguardar'])){
    $name_campaign = $_POST['name_campaign'];
    $category = $_POST['rudr_select2_tags'];
    $array_cat = json_encode($category);
    $product_ap = $_POST['rudr_select2_posts'];
    $array_product = json_encode($product_ap);
    $points = $_POST['point_value'];
    $euros = $_POST['euro_value'];
    $date_init = $_POST['date_init'];
    $date_fin = $_POST['date_fin'];
    
    $datos = [
        'IdCampaign' => null,
        'title_campaign' => $name_campaign,
        'category_campaign' => $array_cat,
        'product_campaign' => $array_product,
        'points' => $points,
        'euros' => $euros,
        'fecha_inicio' => $date_init,
        'fecha_final' => $date_fin,
        'status' => true,
    ];
    $respuesta =  $wpdb->insert($tabla_campaign,$datos);
}

//actualizar configuracion
if(isset($_POST['btnactualizarconf'])){
    $id = $_POST['IdConfig'];
    $name_campaign = $_POST['name_campaign'];
    $category = $_POST['rudr_select2_tags'];
    $array_cat = json_encode($category);
    $product_ap = $_POST['rudr_select2_posts'];
    $array_product = json_encode($product_ap);
    $points = $_POST['point_value'];
    $euros = $_POST['euro_value'];
    $date_init = $_POST['date_init'];
    $date_fin = $_POST['date_fin'];
    

    $wpdb->update($tabla_campaign, 
                  array( 
                        'IdCampaign'=>$id,
                        'title_campaign' => $name_campaign,
                        'category_campaign' => $array_cat,
                        'product_campaign' => $array_product,
                        'points' => $points,
                        'euros' => $euros,
                        'fecha_inicio' => $date_init,
                        'fecha_final' => $date_fin,
                        'status' => true,), 
                        
                   array('IdCampaign'=>$id));
}

if(isset($_POST['btn_borrar'])){
    $id = $_POST['IdCampaign'];
    $wpdb->delete( $tabla_campaign, array( 'IdCampaign' => $id ) );
}
//actualizar configuracion


$query_conf = "SELECT * FROM $tabla_campaign";
$list_conf = $wpdb->get_results($query_conf,ARRAY_A);

    ?>

<div class="wrap">

<?php include(AP_POINTS_URL.'admin/partials/nav.php');
if(empty($list_conf) or isset($_POST['btn_editar'])){
?>

	<div class="tab-content">
		<div id="tab-3" class="tab-pane active">
			
            <form class="config_ap" method="post">
                        <table class="wp-list-table widefat fixed striped pages">
                            <thead>
                                <th><h3>Agregar campaña</h3></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                
                            </thead>
                            <tbody id="the-list">
                                
                                    <tr>
                                        <td>
                                        <label for="campaign">Nombres de campaña</label>
                                        </td>
                                        <td>  
                                        <input type="text" value="<?php if(!empty($list_conf)) echo $list_conf[0]['title_campaign']; ?>" name="name_campaign" placeholder="Campaña" require>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <label for="rudr_select2_tags">Categoria de productos</label>
                            
                                        </td>
                                        <td>  
                                        <select id="rudr_select2_tags" name="rudr_select2_tags[]" multiple="multiple" style="width:99%;max-width:25em;">'
                                        <?php   $product_terms = get_terms( 'product_cat' );
                                        
                                            foreach($product_terms as $product_term):
                                                $product_exist = in_array($product_term->name, json_decode($list_conf[0]['category_campaign']), $strict = false);?>
                                            
                                            <option  type="checkbox" id="<?php $product_term->term_id;?>" <?php if($product_exist ){ echo 'selected';};?>  value="<?php echo $product_term->name; ?>"><?php echo $product_term->name; ?></option>
                                            
                                        
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
                                        <label for="campaign">Productos especificos</label>

                                        </td>
                                        <td>  
                                        
                                    <div class="form-group">
                        
                                        <select id="rudr_select2_posts" class="search-autocomplete" name="rudr_select2_posts[]" multiple="multiple" style="width:99%;max-width:25em;">
                                        <?php  $products_ids = json_decode($list_conf[0]['product_campaign']);
                                        
                                            foreach($products_ids as $product_id):
                                            ?>
                                            <option  type="checkbox" id="<?php $product_term->term_id;?>" selected  value="<?php echo $product_id; ?>"><?php echo get_the_title( $product_id ); ?></option>
                                            
                                        
                                        <?php endforeach; ?>
                                     
                                    </select>
                                    </div>
                                                
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                    <td>
                                    <label for="point">Puntos por Euros gastados</label>
                            
                                        </td>
                                        <td>  
                                        <input type="number" value="<?php echo $list_conf[0]['points']; ?>" name="point_value" placeholder="Puntos"> X <input type="number" value="<?php echo $list_conf[0]['euros']; ?>" name="euro_value" placeholder="Euros">

                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                    <td>
                                    <label for="fecha">Fecha de campaña</label><br>
                                
                                        </td>
                                        <td>  
                                        <label for="fecha">Inicio</label><br>
                                        <input type="date" value="<?php echo $list_conf[0]['fecha_inicio']; ?>" name="date_init" placeholder="Puntos"> <br>
                                        <label for="fecha">Fin</label><br>
                                        <input type="date" value="<?php echo $list_conf[0]['fecha_final']; ?>" name="date_fin" placeholder="Euros">

                                                
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                    <td>
                                    <?php if ($list_conf != true):?>
                            
                            <button type="submit" class="page-title-action btn-primary_ap" name="btnguardar" id="btnguardar">Guardar</button>
                            <?php else: ?>
                            <input type="hidden" name="IdConfig" value="<?php echo $list_conf[0]['IdCampaign']; ?>">
                            <button type="submit" class="page-title-action btn-primary_ap" name="btnactualizarconf" id="btnactualizarconf">Actualizar</button>
                            <?php endif; ?>
                                        </td>
                                    
                                    </tr>
                                    
                                    
                            </tbody>
                        </table>


            </form>
		</div>
	</div>

    <?php }else{ ?>

        <?php $cat_conf = json_decode($list_conf[0]['category_campaign']);
             $date_init = date_create($list_conf[0]['fecha_inicio']);
             $date_fin = date_create($list_conf[0]['fecha_final']);
        ?>
    <div class="tab-content">
		<div id="tab-4" class="tab-pane active">
			
        <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <th>Nombre de Campaña</th>
                    <th>Fecha</th>
                    <th>Categoria</th>
                    <th>Puntos por Euros</th>
                    <th>Status</th>
                    
                </thead>
                <tbody id="the-list">
                       
                        <tr>
                            <td><strong>
                                <?php echo $list_conf[0]['title_campaign']; ?></strong>
                            <div class="row-actions">
                                <span class="edit">
                                <form id="ap_form" action="" method="post">
                                        <input type="hidden" name="IdCampaign" value="<?php echo $list_conf[0]['IdCampaign'];?>">
                                         <button type="submit" class="submitdelete_ap edit_ap" name="btn_editar" id="btnrestar">Editar </button> | 
                                         <button type="submit" class="submitdelete_ap" name="btn_borrar" id="btnrestar">Borrar</button>
                                    </form>
                                     </span>
                                    </span><span class="view"></span></div>
                        </td>
                            <td class="date column-date" data-colname="Fecha">
                                <strong>Inicia  </strong>  <?php echo  date_format($date_init, 'd/m/Y' );?> <br>
                                <strong>Finaliza </strong>  <?php echo date_format($date_fin, 'd/m/Y' );?>
                            </td>
                            <td class="categories column-categories" data-colname="Categorías">
                               <?php  foreach($cat_conf as $cat): ?>
                                <a href=""><?php echo  $cat; ?></a>, 
                                <?php endforeach; ?>
                            </td>
                            <td class="date column-date" data-colname="Fecha"><strong><?php echo $list_conf[0]['points'];?></strong>  Puntos x <strong><?php echo $list_conf[0]['euros'];?></strong> Euro</td>
                            <td>
                                <form id="form_status"  method="POST">
                                <input id="checkbox_id" type="hidden" value="<?php echo $list_conf[0]['IdCampaign']; ?>" name="IdCampaign">
                                <input id="status_check"  type='checkbox' class="wppd-ui-toggle" name="status_check" value='1' <?php  if ( $list_conf[0]['status'] ) echo 'checked="checked"'; ?> /></td>
                             
                            </form>
                        </tr>
                       
                </tbody>
            </table>
            </div>
	</div>

<?php }?>

</div>




<script>    
jQuery(function($){
    var url_path = '<?php echo admin_url("admin-ajax.php");?>'
    var data = 0;
    var checkbox = $("#status_check");
    var id = $("#checkbox_id");
        $(":checkbox#status_check").change(function(){
            
            if(checkbox.prop('checked')){
                var value_checkbox = 1;
            }else{
                var value_checkbox = 0;
            }
                var data = {
                    'action' : 'ap_update_status',
                    'IdCampaign': id.val(),
                    'status_check':  value_checkbox         
                    };
            jQuery.post(url_path, data, function(response) {
                console.log(response);
            });
   		});



});
</script>
