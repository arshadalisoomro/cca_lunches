<?php

class LunchOrdersModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
    public function getLunchDates() {
        $sth = $this->db->prepare("SELECT DISTINCT(orderDate) FROM los_orders ORDER BY orderDate DESC");
        $sth -> execute();
        return $sth -> fetchAll();
    }
    /**
     **************************
     */
    public function getLunchOrders($dateYMD) {
        $res = '';

        $sth = $this->db->prepare("SELECT o.id,CONCAT(u.lastName,', ',u.firstName) AS name,totalPrice,shortDesc,statusCode,
			CONCAT(u1.lastName,', ',u1.firstName) AS teacherName,u1.id AS teacherID,CONCAT(gl.gradeDesc,' (',gl.grade,')') AS grade
			FROM los_orders o
			INNER JOIN los_users u ON o.userID=u.id
			INNER JOIN los_users u1 on u.teacherID=u1.id
			INNER JOIN los_gradelevels gl ON u.gradeID=gl.id
			WHERE orderDate=:dateYMD
			ORDER BY name");
        $sth -> execute(array(':dateYMD' => $dateYMD));
        $orders = $sth -> fetchAll();

        $orderdate = new DateTime($dateYMD);
        $today = new DateTime();

        if ($orderdate->getTimestamp() > $today->getTimestamp()) {
            $actionbtn = '<button id="btnDoAction" type="button" class="btn btn-warning">Lock This Date (No New Orders); Set Status to "Ordered"</button>';
            foreach ($orders as $order) {
                if ($order->statusCode == 1)
                    $actionbtn = '<button id="btnDoAction" type="button" class="btn btn-success">Unlock This Date (Allow New Orders); Set Status to "Scheduled"</button>';
                break;
            }
            $res .= '<label for="btnDoAction">Action</label>';
            $res .= $actionbtn.'<br /><br />';
        }

        $res .= '<table id="lunchorderstable" class="table table-bordered table-header table-condensed">';
        $res .= '<thead><tr><th>Name</th><th>Order Description</th><th>Teacher</th><th>Grade</th><th>Status</th></tr></thead>';

        foreach ($orders as $order) {
            $res .= '<tr>';
            $teacher = '';
            $grade = '';
            $status = '<span style="color:#008800;">- Scheduled -</span>';
            if ($order->statusCode == 1)
                $status = '<span style="color:#EB9316;">- Ordered -</span>';
            if ($order->teacherName != '(unassigned), (unassigned)') {
                $teacher = $order->teacherName;
                $grade = $order->grade;
            }
            $res .= '<td>'.$order->name.'</td><td>'.$order->shortDesc.'</td><td>'.$teacher.'</td><td>'.$grade.'</td><td>'.$status.'</td>';
            $res .= '</tr>';

        }
        $res .= '</table>';
        $res .= '<div style="text-align:right;margin: 8px 10px 0 0;font-weight:bold;color:#cc0000;">'.count($orders).' Orders</div>';

        return $res;
    }
    /**
     **************************
     */
    public function setStatusOrdered($dateYMD) {
        $sth = $this->db->prepare("UPDATE los_lunchdates SET ordersPlaced=NOW() WHERE provideDate=:dateYMD");
        $sth -> execute(array(':dateYMD' => $dateYMD));
        $sth = $this->db->prepare("UPDATE los_orders SET statusCode=1 WHERE orderDate=:dateYMD");
        $sth -> execute(array(':dateYMD' => $dateYMD));
    }
    /**
     **************************
     */
    public function setStatusScheduled($dateYMD) {
        $sth = $this->db->prepare("UPDATE los_lunchdates SET ordersPlaced=NULL WHERE provideDate=:dateYMD");
        $sth -> execute(array(':dateYMD' => $dateYMD));
        $sth = $this->db->prepare("UPDATE los_orders SET statusCode=0 WHERE orderDate=:dateYMD");
        $sth -> execute(array(':dateYMD' => $dateYMD));
    }
}