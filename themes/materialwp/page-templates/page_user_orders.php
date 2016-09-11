<?php
/*
Template Name: User Orders
*/

get_header();
?>

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
                                <li><a href="http://localhost/wordpress/?page_id=138">Wish List</a></li>
                                <li><a href="http://localhost/wordpress/?page_id=136">Your Listings</a></li>
                                <li class="active"><a href="http://localhost/wordpress/?page_id=140 ">Orders</a></li>
                            </ul>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">My orders: <?php echo $rental_count?></div>
                            <table>
                                <tbody>
                                <tr>
                                    <td style="width: 2%"></td>
                                    <td style="width: 96%">
                                        <div class="panel-body">
                                            <div class="list-group">
                                                <?php for($i=0;$i<2;$i++) { ?>
                                                    <div class="list-group-item">
                                                        <div class="row-content">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 30%">
                                                                        <img style="height: 200px; width: 300px" src="http://localhost/wordpress/wp-content/uploads/2016/05/4242805-house-1-300x200.jpg" alt="icon">
                                                                    </td>
                                                                    <td valign="top" style="width: 70%">
                                                                        <ul style="list-style-type: none;">
                                                                            <li>
                                                                                <h3 class="list-group-item-heading">Warm & Quite & Big House</h3>
                                                                            </li>
                                                                            <li>
                                                                                <p class="list-group-item-text"><span style="color: grey">310 Alert Rd, South Yarra</span></p>
                                                                                <p class="list-group-item-text"><span style="color: grey">$499 AUD per week</span></p>
                                                                            </li>
                                                                            <li>
                                                                                <p> </br></p>
                                                                                <p> </br></p>
                                                                                <table>
                                                                                    <tbody>
                                                                                        <tr align="middle">
                                                                                            <td style="width: 25%"><span style="color: lightgrey">Completing your application</span></td>
                                                                                            <td style="width: 25%"><span style="color: lightgrey">Waiting for landloard's approval</span></td>
                                                                                            <td style="width: 25%"><span style="color: #ff5722">Securing your next home</span></td>
                                                                                            <td style="width: 25%"><span style="color: lightgrey">Hi5Fang! Get Ready to move!</span></td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </li>
                                                                            <li>
                                                                                <div class="bs-component">
                                                                                    <div class="progress">
                                                                                        <div class="progress-bar progress-bar-info" style="width: 25%"></div>
                                                                                        <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                                                                        <div class="progress-bar progress-bar-warning" style="width: 25%"></div>
                                                                                        <div class="progress-bar progress-bar-danger" style="width: 25%;visibility: hidden"></div>
                                                                                    </div>
                                                                                </div>
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
                                    <td style="width: 2%"></td>
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
