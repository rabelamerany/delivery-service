<hr>
<!-- First Section -->
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="dashboard_graph">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <form action="<?= base_url('drivers/driver_cash_in_data') ?>" method="post" id="form">
                    <div class="form-group">
                        <label for="driver">Driver</label>
                        <select name="driver" id="driver" class="form-control" required>
                            <option value="" selected disabled>Select Driver</option>
                            <?php foreach ($drivers as $driver) { ?>
                                <option value="<?= $driver->driverNumber ?>"><?= $driver->driverFullName ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <div class="input-group date" data-provide="datepicker">
                            <input type="text" class="form-control" id="start_date" readonly required>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <div class="input-group date" data-provide="datepicker">
                            <input type="text" class="form-control" id="end_date" readonly required>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Calculate Cash IN</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <tr>
                            <th>Number of Orders:</th>
                            <td id="number_orders" style="font-weight: bold"></td>
                        </tr>
                        <tr>
                            <th>Cash IN Amount:</th>
                            <td id="cash_in_amount" style="font-weight: bold"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<!-- /First Section -->
