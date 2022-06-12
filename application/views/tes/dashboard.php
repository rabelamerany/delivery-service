<!-- First Section -->
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="dashboard_graph">
			<div class="row x_title">
				<div class="col-md-6"><h3>Daily Orders</h3></div>
				<div class="col-md-6">
					<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
						<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
						<span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
					</div>
				</div>
                <div class="clearfix"></div>
			</div>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<div id="chart_plot_01" class="demo-placeholder"></div>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-12 bg-white">
				<div class="x_title">
                    <h5>Top 3 Drivers</h5>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" id="top_drivers"></div>
				<div class="x_title">
                    <h5>Available Drivers Now</h5>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" id="available_drivers_now"></div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<!-- /First Section -->

<br>

<!-- Second Section -->
<div class="row">
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile fixed_height_320">
			<div class="x_title">
                <h2>Dispatcher Performance</h2>
                <div class="clearfix"></div>
            </div>
			<div class="x_content" id="top_dispatchers"></div>
		</div>
	</div>

	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile fixed_height_320 overflow_hidden">
			<div class="x_title">
                <h2>Driver Workload</h2>
                <div class="clearfix"></div>
            </div>
			<div class="x_content">
				<table style="width:100%">
					<tr>
						<td><canvas id="chart_doughnut" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas></td>
						<td><table class="tile_info" id="label_doughnut"></table></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile">
            <div class="x_title">
                <h2>New Orders</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="dashboard-widget-content">
                    <ul class="list-unstyled timeline widget" id="activities"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Second Section -->
