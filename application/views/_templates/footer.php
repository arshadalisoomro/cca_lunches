    <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo URL; ?>public/select2/select2.min.js"></script>
    <script type="text/javascript" src="<?php echo URL; ?>public/js/vendor/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo URL; ?>public/js/vendor/jquery.formatCurrency-1.4.0.js"></script>
    <script type="text/javascript" src="<?php echo URL; ?>public/js/vendor/jquery.blockUI.min.js"></script>

    <script type="text/javascript" src="<?php echo URL; ?>public/js/common2.js"></script>
    <?php if (Session::get('user_account_type') == ACCOUNT_TYPE_ADMIN):?>
        <script type="text/javascript" src="<?php echo URL; ?>public/js/admin1.js"></script>
    <?php endif; ?>
</body>
</html>
