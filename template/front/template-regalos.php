<?php
include(AP_POINTS_URL.'/form/form.php');


    $reagalos = get_posts(array(
        'post_type' => 'ap_regalo',
        'meta_key' => '',
        'meta_value' => ''
    ));
    if ($reagalos) {?>


             <p>Cuneta con:  <strong>(<?php echo $total_point;?> puntos )</strong> </p>
 
     

        <div class="container">
            <div class="row">
                <?php foreach ($reagalos as $post) { setup_postdata( $post ); 
                    $points = get_post_meta($post->ID, 'points_sale', true);?>
                    <div class="col">
                        <ul class="reagalos d-flex">
                            <li class="judge">
                                <h4 class="text-center"><a href="<?php the_permalink() ?>"><?php echo $post->post_title; ?></a></h4>
                                <img src="" alt="" />
                                <p><?php the_content(); ?></p>
                                <p class="precio">Puntos <strong><?php echo $points;?></strong></p>
                                <form id="ap_form_canje" action="" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $id_user;?>">
                                    <input type="hidden" name="points" value="<?php echo $points;?>">
                                    <button type="submit" class="page-title-action" name="btncanjear" id="btncanjear">Canjear</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php } wp_reset_postdata(); ?>
            </div> <!-- end row -->
        </div> <!-- end container -->

    <?php };
