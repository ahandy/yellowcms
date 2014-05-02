<table class='datatable' rel='<?php echo HTTP . "rows/update/{$this -> table}"; ?>'>
    <thead>
        <tr>
        <?php foreach($this -> columns as $column) echo "<th data-type='{$column['column_type']}'>{$column['column_name']}</th>"; ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach($this -> rows as $id => $row) {
            echo "<tr data-id='{$id}'>";
            $i = 0; 
            foreach($row as $k => $field) {
                echo "<td data-column='" . $this -> columns[$i]['column_name'] . "'>{$field}</td>";
                $i++;
            }
            echo "<td><a href='" . HTTP . "rows/edit/{$this -> table}/{$id}' class='edit'>Edit</a><a class='remove'>Remove</a></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>