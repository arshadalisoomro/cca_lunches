<div class="container page width400">
    <div class="pull-right" style="font-size: 15px;">
        <a href="mailto:support@ccalunches.com?Subject=CCA Lunch Program Question" target="_top">support@ccalunches.com</a>
    </div>
    <h2>Contact Us</h2>
    <br />

    <form role="form" method="post" action="<?php echo URL; ?>contactus/contactus" name="contact_form">
        <div class="form-group">
            <label for="sendername">Your Name</label>
            <input type="text" class="form-control" id="sendername" name="sendername" required autocomplete="on" value="<?php echo Session::get('sendername') ?>">
        </div>
        <div class="form-group">
            <label for="senderemail">Your Email Address</label>
            <input type="email" class="form-control" id="senderemail" name="senderemail" required autocomplete="on" value="<?php echo Session::get('senderemail') ?>">
        </div>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required
                   value="<?php if ((Session::get('subject')) == '') echo 'CCA Lunch Program Question'; else echo Session::get('subject'); ?>">
        </div>
        <div class="form-group">
            <label for="msg">Message</label>
            <textarea id="msg" name="msg" class="form-control" rows="5" required autocomplete="on"><?php echo Session::get('msg') ?></textarea>
        </div>

        <br />

        <div class="form-group">
            <label for="captcha">Please enter the security characters</label>
            <img style="margin: 0 0 5px;"src="<?php echo URL; ?>login/showCaptcha" />
            <!--<a href="javascript: window.location.reload()">&nbsp;Reload</a>-->
            <input type="text" name="captcha" required class="form-control" />
        </div>

        <br />

        <?php $this->renderFeedbackMessages(); ?>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>
</div>
