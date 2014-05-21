<div class="container page width400">
    <div class="pull-right"><a href="<?php echo URL; ?>login/requestpasswordreset">Forgot your password?</a></div>
    <h2>Login</h2>
    <br />
    <form namerole="form" action="<?php echo URL; ?>login/login" method="post">
        <div class="form-group">
            <label>Username (your email address)</label>
            <input type="text" name="user_name" class="form-control" required autofocus/>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="user_password" class="form-control" required />
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
                <p style="font-style: italic;font-size: 12px;">(Do not use Remember Me on shared computers)</p>
            </div>
        </div>
		<input type="hidden" name="local_tzo" id="local_tzo" value="0"> 
		
        <div style="text-align:center;margin-top: 25px;">
			<button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </form>

    <?php if (FACEBOOK_LOGIN == true) { ?>
    <div class="login-facebook-box">
        <h1>or</h1>
        <a href="<?php echo $this->facebook_login_url; ?>" class="facebook-login-button">Log in with Facebook</a>
    </div>
    <?php } ?>

</div>

<script type="text/javascript">
	window.onload = function() { 
		document.getElementById('local_tzo').value = new Date().getTimezoneOffset()/60;
	}
</script>	