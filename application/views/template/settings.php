<form method="post" class="form-horizontal form-label-left">
    <?php foreach ($settings as $key => $value) { ?>
    <div class="form-group">
        <label for="<?= $key ?>" class="control-label col-md-3 col-sm-3 col-xs-12"><?= ucfirst(substr($key, 4)) ?></label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input id="<?= $key ?>" name="<?= $key ?>" class="form-control" value="<?= $value ?>" />
        </div>
    </div>
    <?php } ?>
    <div class="ln_solid"></div>
    <div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </div>
</form>