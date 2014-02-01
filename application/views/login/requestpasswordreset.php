<div class="container page width500">
    <h2>Request a password reset</h2>

    <form method="post" action="<?php echo URL; ?>login/requestpasswordreset_action" name="password_reset_form">
        <div class="form-group">
            <label for="password_reset_input_username">
                Enter your email address and you'll get an email with instructions:
            </label>
            <input id="password_reset_input_username" class="form-control" type="text" name="user_name" required />
        </div>
        <?php $this->renderFeedbackMessages(); ?>
        <div style="text-align:center;">
            <button class="btn btn-primary" type="submit" name="request_password_reset" value="Reset my password">Submit</button>
        </div>

    </form>
</div>
