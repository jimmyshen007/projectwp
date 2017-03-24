<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/13/17
 * Time: 1:22 AM
 */
get_header();
$tdir = get_template_directory_uri();
?>
    <style>
        .centerize{
            background-color: white;
            text-align: center;
        }
        .panel-custom{
            width: 100% !important;
            text-align: center;
        }
    </style>
    <div class="container">
        <div class="row">
            <div id="primary" class="col-md-12 col-lg-12">
                <main id="main" class="site-main" role="main">
                    <div class="panel panel-default panel-custom">
                        <div class="panel-heading">
                            <img src="" />
                            <h2><?php echo __('How Ulieve Works', 'materialwp') ?></h2></div>
                        <div class="panel-body centerize">
                            <img style="margin-left: auto; margin-right: auto" width="100%" height="auto" src="<?php echo $tdir . '/images/flowchart.png' ?>" />
                        </div>
                    </div>
                </main><!-- #main -->
            </div><!-- #primary -->
        </div> <!-- .row -->
    </div> <!-- .container -->

<?php get_footer(); ?>