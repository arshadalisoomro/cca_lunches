<div class="container page width500">
    <div class="pull-right"><a href="<?php echo URL; ?>login/index">Back to Login Page</a></div>
    <h2>Set New Password</h2>

    <?php $this->renderFeedbackMessages(); ?>

    <form role="form" method="post" action="<?php echo URL; ?>login/setnewpassword" name="new_password_form">
        <div class="form-group">
            <label for="reset_input_password_new">New password (min. 6 characters!)</label>
            <input id="reset_input_password_new" class="form-control" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />
        </div>
        <div class="form-group">
            <label for="reset_input_password_repeat">Repeat new password</label>
            <input id="reset_input_password_repeat" class="form-control" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
        </div>

        <input type='hidden' name='user_name' value='<?php echo $this->user_name; ?>' />
        <input type='hidden' name='user_password_reset_hash' value='<?php echo $this->user_password_reset_hash; ?>' />

        <div style="text-align:center;">
            <button class="btn btn-primary" type="submit" name="submit_new_password">Submit</button>
        </div>
    </form>
</div>

