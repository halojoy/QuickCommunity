<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['disp_ord'])) {
    $neworder = $_POST['in'];
    foreach ($neworder as $fid => $disp_order) {
        $neworder[$fid] = trim($disp_order);
        if (!is_numeric($neworder[$fid]) || empty($neworder[$fid])) {
            echo 'Forum order value should be a number!';
            exit();
        }
    }
    foreach ($neworder as $fid => $disp_ord) {
        $sql = "UPDATE forums SET f_order=$disp_ord WHERE fid=$fid";
        $ret = $this->pdo->querySQL($sql);
    }

}

$sql = "SELECT * FROM forums ORDER BY f_order";
$forums = $this->pdo->querySQL($sql)->fetchAll();
?>
        <span class="boldy">Forum Display Order</span>
        <br><br>
        <form method="post">
            <table>
                <?php
                foreach ($forums as $forum) {
                    echo "<tr>\n";
                    echo "<td>" . $forum->f_name . "</td>";
                    echo '<td>
                            <input type="text" size="2" maxlength="2" name="in['.$forum->fid.']"
                            value="'.$forum->f_order.'" required />';
                    echo '</td>'."\n";
                    echo "</tr>\n";
                }
                ?>
            </table>
            <br>
            <input type="submit" value="Submit">
            <input type="hidden" name="act" value="forumorder">
            <input type="hidden" name="disp_ord" value="1">
        </form>
        <br>
