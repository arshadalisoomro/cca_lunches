<div class="container page">

    <img height="83" width="124" alt="welcome" src="<?php echo URL; ?>public/img/lunch124x83.jpg" style="float: right;border:1px solid #000;margin: 0 0 10px 10px;">
    <h2>Welcome to the CCA Lunch Ordering System</h2>
    <br />

    <?php if (Session::get('user_logged_in') == false):?>
        <h3>Returning Users</h3>
        <p>
            <a href="<?php echo URL; ?>login/index" class="btn btn-primary" role="button">Login</a>
        </p>
        <br />
        <h3>New User or Forgot Your Password?</h3>
        <p>
            <a href="<?php echo URL; ?>login/requestpasswordreset" class="btn btn-primary" role="button">Create / Reset My Password</a>

        </p>
        <br />
    <?php endif; ?>

    <?php if (Session::get('user_logged_in') == true):?>
        <h3>You Are Currently Logged In</h3>
        <p style="color:#336699;font-size:20px;margin: 0 0 20px;"><?php echo Session::get('account_name') ?></p>

        <p>
            Select a choice from the menu in the upper right corner, or <br /><br />
            <a href="<?php echo URL; ?>login/logout" class="btn btn-primary" role="button">Logout</a>
        </p>
        <br />
    <?php endif; ?>

    <h3>Payments</h3>
    <p>
        Payments can be made in the following ways:
    </p>
    <ul>
        <li>
            You can use PayPal to pay for lunch on this website, or
        </li>
        <li>
            Your tuition bill will reflect the amount you owe at the end of the month.
        </li>
    </ul>
    <br />
    <h3>About The Lunch Ordering System</h3>
    <p>
        This system works in "real-time", which means that you can add or delete lunch orders up until the time orders are placed with our vendors. So for example if you place an order but then your child will end up missing school and therefore that lunch order, you will be able to cancel the order if it has not already been placed with the vendor.
    </p>
    <p>
        Questions? Problems? Please feel free to <a href="<?php echo URL; ?>contactus/index">contact us</a>.
    </p>
    <p>
        Thanks for using the Lunch Ordering System!
    </p>
    <p>
        <em>The CCA Lunch Ordering Team</em>
    </p>
</div>