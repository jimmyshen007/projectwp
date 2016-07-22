<?php
/*
Template Name: Wish List
*/

get_header();
?>
<script>
    $(document).ready(function(){
        $("#btnCheck1").click(function(){
            $.post(

                 "http://localhost:3000/index",

                 function(result){
                $("#testdiv").html(result.str)},"jsonp"
            );
        });
    });
</script>
<div class="container">
    <div class="row">
        <div id="primary" class="col-md-12 col-lg-12">
            <main id="main" class="site-main" role="main">
                <div class="card">
                    <div class="entry-img"></div>
                    <div class="entry-container">
                        <p></p>
                        <div>
                            <ul class="nav nav-pills" style="margin-bottom: 35px; margin-left: 0px;margin-top: -15px;">
                                <li><a href="http://localhost/wordpress/?page_id=118">Profile</a></li>
                                <li class="active"><a href="http://localhost/wordpress/?page_id=138">Wish List</a></li>
                                <li><a href="http://localhost/wordpress/?page_id=136">Your Listings</a></li>
                                <li><a href="http://localhost/wordpress/?page_id=140 ">Orders</a></li>
                            </ul>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">My Wish Lists: <?php echo $rental_count?></div>
                            <table>
                                <tbody>
                                <tr>
                                    <td style="width: 8%"></td>
                                    <td style="width: 80%">
                                        <div class="panel-body">
                                            <div class="list-group">
                                                <?php for($i=1;$i<=2;$i++) { ?>
                                                    <div class="list-group-item">
                                                        <div class="row-content">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <td style="40%">
                                                                        <img style="height: 200px; width: 300px" src="http://localhost/wordpress/wp-content/uploads/2016/05/4242805-house-1-300x200.jpg" alt="icon">
                                                                    </td>
                                                                    <td valign="top" width="60%">
                                                                        <ul style="list-style-type: none;margin-bottom: 0px">
                                                                            <li>
                                                                                <h3 class="list-group-item-heading">Warm & Quite & Big House</h3>
                                                                            </li>
                                                                            <li>
                                                                                <p class="list-group-item-text"><span style="color: grey">310 Alert Rd, South Yarra</span></p>
                                                                            </li>
                                                                            <li><p></p></li>
                                                                            <li><p></br></p></li>
                                                                            <li>
                                                                                <div id="testdiv"></div>
                                                                                <p class="list-group-item-text"><span class="list-group-item-heading">$499 AUD</span><span style="color: grey"> per week</span></p>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0)" id="<?php echo"btnCheck".$i?>" class="btn btn-raised btn-default" style="margin-top:18px;margin-bottom: 0px">Check Price & Availability</a>
                                                                            </li>
                                                                        </ul>

                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-separator"></div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 12%"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main><!-- #main -->
        </div><!-- #primary -->

    </div> <!-- .row -->
</div> <!-- .container -->

<?php get_footer(); ?>
