<?php

// get the feedback (they are arrays, to make multiple positive/negative messages possible)
$feedback_positive = Session::get('feedback_positive');
$feedback_negative = Session::get('feedback_negative');

// echo out positive messages
if (isset($feedback_positive)) {
    echo '<div class="alert alert-success alert-dismissable">';
    echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    foreach ($feedback_positive as $feedback) {
        echo '<div>'.$feedback.'</div>';
    }
    echo '</div>';
}

// echo out negative messages
if (isset($feedback_negative)) {
    echo '<div class="alert alert-danger alert-dismissable">';
    echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    foreach ($feedback_negative as $feedback) {
        echo '<div>'.$feedback.'</div>';
    }
    echo '</div>';
}
