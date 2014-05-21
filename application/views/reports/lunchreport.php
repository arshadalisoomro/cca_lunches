<div class="container page">
	<?php echo $this->userLunchReport; ?>
</div>

<script type="text/javascript">
	if (typeof $ != 'undefined') {
		$(document).ready(function() {
			"use strict";
			$(document).on('click', '.btn-print', function (e) {
				var f = $('.new-tab-opener');
				f.attr('action', $(this).attr('data-href'));
				f.submit();
			});
		});
	}
</script>