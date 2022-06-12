<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo $title ?></title>

        <!-- Import CSS -->
        <link href="<?php echo base_url('vendors/bootstrap/dist/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('vendors/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('vendors/bootstrap-daterangepicker/daterangepicker.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker.css') ?>" rel="stylesheet">
        <!-- Datatables -->
        <link href="<?php echo base_url('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') ?>"
              rel="stylesheet">
        <link href="<?php echo base_url('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css') ?>"
              rel="stylesheet">
        <link href="<?php echo base_url('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css') ?>"
              rel="stylesheet">
        <link href="<?php echo base_url('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css') ?>"
              rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="<?php echo base_url('assets/css/custom.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('vendors/schedule/style.css') ?>" rel="stylesheet">
        <style>
            <?php echo $style ?>
        </style>
        <!-- Grocery CRUD CSS -->
        <?php if (isset($css_files)) foreach ($css_files as $file): ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>"/>
        <?php endforeach; ?>
        <!-- /Import CSS -->
    </head>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <?php echo $sidenavs ?>
                <?php echo $navs ?>
                <!-- Page Content -->
                <div class="right_col" role="main">
                    <div>
                        <div class="page-title">
                            <div class="title_left">
                                <h3><?php echo $header ?></h3>
                            </div>
                            <div class="title_right">
                                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                                    <!--
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search for...">
                                        <span class="input-group-btn"><button class="btn btn-default" type="button">Go!</button></span>
                                    </div>
                                -->
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php echo $content ?>
                    </div>
                </div>
                <!-- /Page Content -->
                <!-- Footer Content -->
                <footer>
                    <div class="pull-right">
                        <?php echo $footer; ?>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /Footer Content -->
            </div>
        </div>

        <!-- Import Javascript -->
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="<?php echo base_url('vendors/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('vendors/fastclick/lib/fastclick.js') ?>"></script>
        <script src="<?php echo base_url('vendors/Chart.js/dist/Chart.min.js') ?>"></script>
        <script src="<?php echo base_url('vendors/iCheck/icheck.min.js') ?>"></script>
        <!-- Flot -->
        <script src="<?php echo base_url('vendors/Flot/jquery.flot.js') ?>"></script>
        <script src="<?php echo base_url('vendors/Flot/jquery.flot.pie.js') ?>"></script>
        <script src="<?php echo base_url('vendors/Flot/jquery.flot.time.js') ?>"></script>
        <script src="<?php echo base_url('vendors/Flot/jquery.flot.stack.js') ?>"></script>
        <script src="<?php echo base_url('vendors/Flot/jquery.flot.resize.js') ?>"></script>
        <!-- Flot Plugins -->
        <script src="<?php echo base_url('vendors/flot-spline/js/jquery.flot.spline.min.js') ?>"></script>
        <!-- Bootstrap Date Range Picker -->
        <script src="<?php echo base_url('vendors/moment/min/moment.min.js') ?>"></script>
        <script src="<?php echo base_url('vendors/bootstrap-daterangepicker/daterangepicker.js') ?>"></script>
        <!-- Bootstrap Date Picker -->
        <script src="<?php echo base_url('vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.js') ?>"></script>
        <!-- Custom JS -->
        <script src="<?php echo base_url('assets/js/custom.js') ?>"></script>
        <script src="<?php echo base_url('vendors/schedule/code.js') ?>"></script>
        <!-- Grocery CRUD JS -->
        <?php if (isset($js_files)) foreach ($js_files as $file): ?>
            <script src="<?php echo $file; ?>"></script>
        <?php endforeach; ?>
        <!-- /Import Javascript -->
        <script type="text/javascript">
            <?php echo $javascript ?>
        </script>
    </body>
</html>
