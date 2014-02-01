<div class="container page">
    <h2>Lunch Order Maintenance</h2>
    <hr />
    <label for="lunchordersselectdate">Date</label>
    <select id="lunchordersselectdate">
        <option value="0">[Please Select]</option>
        <?php
        foreach ($this->lunchdates as $lunchdate) {
            $date = new DateTime($lunchdate->orderDate);
            echo '<option value="'.$date->format('Y-m-d').'">'.$date->format('l, F jS, Y').'</option>';
        }
        ?>
    </select>
    <br /><br />
    <div id="lunchordersresults">
        <table id="lunchorderstable" class="table table-bordered table-header table-condensed table-striped">
            <thead><tr><th>Name</th><th>Order Description</th><th>Teacher</th><th>Grade</th><th>Status</th></tr></thead>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
        </table>
    </div>
</div>
