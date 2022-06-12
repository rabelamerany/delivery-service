<?php
$CI       =& get_instance();
$userdata = $CI->session->userdata();
$name     = '';
$img      = '';
$query    = $userdata['query'];

if (isset($userdata) && isset($userdata['first_name']) && isset($userdata['email'])) {
	$name = $userdata['first_name'];
	$img  = 'https://www.gravatar.com/avatar/' . md5($userdata['email']);
}

$CI->load->helper('general_helper');
?>
<div class="col-md-3 left_col menu_fixed">
	<div class="left_col scroll-view">
		<div class="navbar nav_title" style="border: 0;">
			<div class="profile_pic">
				<br>
				<a href="<?php echo base_url(); ?>" class="site_title">
					<img src="<?php echo base_url('assets/images/logo.png') ?>" height="35px">
					<span style="font-size:20px;"></span>
				</a>
			</div>
			<div class="profile_info">
				<span>Welcome,</span>
				<h2><?= $name ?></h2>
			</div>
		</div>
		<!--
		<div class="navbar nav_title" style="border: 0;">
				<span style="font-size: 20px;"></span>
		</div>
		<div class="clearfix"></div>-->
		<!-- menu profile quick info -->
		<div class="profile clearfix">
			<!--<div class="profile_pic">
				<img src="<?= $img ?>" alt="..." class="img-circle profile_img">
			</div>
			<div class="profile_info">
				<span>Welcome,</span>
				<h2><?= $name ?></h2>
			</div>-->
		</div>
		<!-- /menu profile quick info -->
		<br><br>
		<!-- Sidebar Menu -->
		<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
			<div class="menu_section">
				<h3>Menu</h3>
				<ul class="nav side-menu">
					<li><a href="<?php echo base_url('DashboardCustomers/') ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
					<li><a href="<?php echo base_url('EcomOrdersCustomers/today') ?>"><i class="fa fa-dashboard"></i> Today's orders</a></li>
					<li>
						<a><i class="fa fa-shopping-cart"></i> E-Com Orders <span class="fa fa-chevron-down"></span></a>
						<ul class="nav child_menu">
							<li><a href="<?php echo base_url('ecomOrdersCustomers/index/add') ?>">Add Orders</a></li>
							<li><a href="<?php echo base_url('ecomOrdersCustomers/') ?>">List Orders</a></li>
							<li><a href="<?php echo base_url('productcustomers/') ?>">Show Products</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<!-- /Sidebar Menu -->
	</div>
</div>
