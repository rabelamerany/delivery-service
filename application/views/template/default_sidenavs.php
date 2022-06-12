<?php
$CI =& get_instance();
$userdata = $CI->session->userdata();
$name = '';
$img = '';
$query = $userdata['query'];

if (isset($userdata) && isset($userdata['first_name']) && isset($userdata['last_name']) && isset($userdata['email'])) {
	$name = $userdata['first_name'] . ' ' . $userdata['last_name'];
	$img = 'https://www.gravatar.com/avatar/' . md5($userdata['email']);
}

$CI->load->helper('general_helper');
?>
<div class="col-md-3 left_col menu_fixed">
	<div class="left_col scroll-view">
		<div class="navbar nav_title" style="border: 0;">
			<a href="<?php echo base_url(); ?>" class="site_title">
				<img src="<?php echo base_url('assets/images/logo.png') ?>" height="35px">
				<span style="font-size: 20px;"></span>
			</a>
		</div>
		<div class="clearfix"></div>
		<!-- menu profile quick info -->
		<div class="profile clearfix">
			<div class="profile_pic">
				<img src="<?= $img ?>" alt="..." class="img-circle profile_img">
			</div>
			<div class="profile_info">
				<span>Welcome,</span>
				<h2><?= $name ?></h2>
			</div>
		</div>
		<!-- /menu profile quick info -->
		<br>
		<!-- Sidebar Menu -->
		<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
			<div class="menu_section">
				<h3>Menu</h3>
				<ul class="nav side-menu">
					<?php if (!(is_granted('Controller') || is_granted('Driver'))) { ?>
						<li><a href="<?php echo base_url('/') ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
						<li>
							<a><i class="fa fa-group"></i> Customers <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li><a href="<?php echo base_url('customers/index/add') ?>">Add Customers</a></li>
								<li><a href="<?php echo base_url('customers/') ?>">Show Customers</a></li>
								<li><a href="<?php echo base_url('BalanceHistory/') ?>">Balance History</a></li>
							</ul>
						</li>
						<li>
							<a><i class="fa fa-shopping-cart"></i> Orders <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li><a href="<?php echo base_url('orders/index/add') ?>">Add Orders</a></li>
								<li><a href="<?php echo base_url('orders/') ?>">Show Orders</a></li>
								<?php if (is_granted('Admin') || is_granted('Dispatcher')) { ?>
								<li><a href="<?php echo base_url('products/') ?>">Show Products</a></li>
								<?php } ?>
								<li><a href="<?php echo base_url('EcomOrders/') ?>">E-Com Orders (<?= $query ?>)</a></li>
							</ul>
						</li>
						<li>
							<a><i class="fa fa-user"></i> Personnel <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<?php if (is_granted('Admin')) { ?>
									<li><a href="<?php echo base_url('users/index/add') ?>">Add Users</a></li>
								<?php } ?>
								<li><a href="<?php echo base_url('users/') ?>">Show Users</a></li>
							</ul>
						</li>
						<li>
							<a><i class="fa fa-truck"></i> Drivers <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li><a href="<?php echo base_url('drivers/index/add') ?>">Add Drivers</a></li>
								<li><a href="<?php echo base_url('drivers/') ?>">Show Drivers</a></li>
								<?php if (is_granted('Admin')) { ?>
									<li><a href="<?php echo base_url('drivers/driver_cash_in') ?>">Driver Cash IN</a>
									</li>
									<li><a href="<?php echo base_url('drivers/externals_payment_due') ?>">Externals
											Wallet</a></li>
								<?php } ?>
							</ul>
						</li>
						<li>
							<a><i class="fa fa-gift"></i>Promotion <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li><a href="<?php echo base_url('coupons/') ?>">Coupons</a></li>	
								<li><a href="<?php echo base_url('promotion/') ?>">Coupon Campaign</a></li>
							</ul>
						</li>
						<li>
							<a><i class="fa fa-dollar"></i> Accounting <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li><a href="<?php echo base_url('invoices') ?>">Invoices</a></li>
								<li><a href="<?php echo base_url('invoicesEcom') ?>">Invoices Ecom</a></li>
								<?php if (is_granted('Admin')) { ?>
									<li><a href="<?php echo base_url('controllers') ?>">Cash In/Out</a></li>
								<?php } ?>
							</ul>
						</li>
					<?php } elseif (is_granted('Driver')) { ?>
						<li><a href="<?php echo base_url('orders') ?>">Show Orders</a></li>
					<?php } elseif (is_granted('Controller')) { ?>
						<li><a href="<?php echo base_url('orders') ?>"><i class="fa fa-list"></i>Show Orders</a></li>
						<li><a href="<?php echo base_url('drivers/driver_cash_in') ?>"><i class="fa fa-dollar"></i>
								Driver Cash IN</a></li>
						<li><a href="<?php echo base_url('invoices') ?>"><i class="fa fa-file"></i> Invoices</a></li>
						<li><a href="<?php echo base_url('controllers') ?>"><i class="fa fa-dollar"></i> Cash In/Out</a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<!-- /Sidebar Menu -->
	</div>
</div>
