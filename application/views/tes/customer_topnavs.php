<!-- Top Nav -->
<div class="top_nav">
	<div class="nav_menu">
		<nav>
			<?php $this->load->helper('general_helper');  ?>
			<div class="nav toggle"><a id="menu_toggle"><i class="fa fa-bars"></i></a></div>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<?php
							$CI =& get_instance();
    						$userdata = $CI->session->userdata();
    						$name = '';
    						$img = '';
    						if(isset($userdata) && $userdata['first_name'] == true && $userdata['email'] == true)
							{
							    $name = $userdata['first_name'];
							    $img = 'https://www.gravatar.com/avatar/'.md5($userdata['email']);
							}
						?>
						<img src="<?= $img ?>" alt="">
						<?= $name ?> <span class="fa fa-angle-down"></span>
					</a>
					<ul class="dropdown-menu dropdown-usermenu pull-right">
						<li><a href="/ecomProfile/index/edit/<?= $userdata['customerNumber'] ?>">Profile</a></li>
						<li><a href="/logincustomers/logout"><i class="fa fa-sign-out pull-right"></i>Logout</a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>
</div>
<!-- /Top Nav -->
