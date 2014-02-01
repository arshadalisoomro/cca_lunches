<div class="container page width500">
    <h3>Send Emails To Lunch System Users</h3>
    <br />

    <form role="form" method="post" action="<?php echo URL; ?>sendemails/domail" name="sendemails_form">

        <div class="form-group">
            <label for="fromemail">From </label> <i>&nbsp;&nbsp;(any user replies will come to this email address)</i>
            <input type="text" class="form-control" id="fromemail" name="fromemail" required autocomplete="on" value="support@ccalunches.com">
        </div>

        <div class="form-group">
            <label for="select-send-mail-to">Send To</label><br />
            <select id="select-send-mail-to" name="select-send-mail-to">
				<option value="1">"FROM" address entered above (for testing purposes)</option>
                <option value="2">New users created today</option>
                <option value="3">All active users (anyone who has ever ordered lunch)</option>
                <option value="4">All users</option>
            </select>
        </div>

        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required
                <?php
                if (isset($_SESSION['sendemails_subject']))
                    echo "value='".$_SESSION['sendemails_subject']."'";
                ?>
            >
        </div>

        <div class="form-group">
            <label for="msg">Message</label>
            <textarea id="msg" name="msg" class="form-control" rows="10" required autocomplete="on"><?php if (isset($_SESSION['sendemails_msg'])) echo $_SESSION['sendemails_msg'];?></textarea>
        </div>

        <br />

        <?php $this->renderFeedbackMessages(); ?>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary">
                Send
            </button>
        </div>
    </form>
</div>
