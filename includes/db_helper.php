<?php 
    function selectDb($conn, $table, $column = '*', $where_condition = [], $order = ''){
        $sql = "SELECT $column FROM `$table`";
        // where condition
        if(!empty($where_condition)){
            $where_arr = [];
            foreach ($where_condition as $key => $value){
                if(is_null($value)){
                    $where_arr[] = "`$key` IS NULL";
                }
                else{
                    $safe_val = mysqli_real_escape_string($conn, $value);
                    $where_arr[] = "`$key` = '$safe_val'";
                }
            }
            $sql .= " WHERE " . implode(" AND ", $where_arr);
        }

        $sql .= " $order"; // thêm thứ tự sắp xếp

        $result = mysqli_query($conn, $sql);
        $data = [];

        if($result && mysqli_num_rows($result) > 0){
            while ($row = mysqli_fetch_assoc($result)){
                $data[] = $row;
            }
        }
        return $data;
    }

    function insertDb($conn, $table, $data){
        $columns = [];
        $values = [];

        foreach($data as $key => $value){
            $columns[] = "`$key`";
            if(is_null($value)){
                $values[] = "NULL";
            }
            else{
                $safe_val = mysqli_real_escape_string($conn, $value);
                $values[] = "'$safe_val'"; 
            }
        }

        $col_str = implode(", ", $columns);
        $val_str = implode(", ", $values);

        $sql = "INSERT INTO `$table` ($col_str) VALUES ($val_str)";
        return mysqli_query($conn, $sql);
    }

    function updateDb($conn, $table, $data, $condition){
        $set_arr = [];
        foreach($data as $key => $value){
            if(is_null($value)){
                $set_arr[] = "`$key` = NULL";
            }
            else{
                $safe_val = mysqli_real_escape_string($conn, $value);
                $set_arr[] = "`$key` = '$safe_val'";
            }
        }
        $set_str = implode(", ", $set_arr);

        $sql = "UPDATE `$table` SET $set_str";
        
        if(!empty($condition)){
            $where_arr = [];
            foreach($condition as $key => $value){
                if(is_null($value)){
                    $where_arr[] = "`$key` IS NULL";
                }
                else{
                    $safe_val = mysqli_real_escape_string($conn, $value);
                    $where_arr[] = "`$key` = '$safe_val'";
                }
            }
            
            $sql .= " WHERE " . implode(" AND ", $where_arr);
        }

        
        return mysqli_query($conn, $sql);
    }

    function deleteDb($conn, $table, $condition){
        $sql = "DELETE FROM `$table`";
        if (empty($condition)) {
            return false; 
        }
        if(!empty($condition)){
            $where_arr = [];
            foreach($condition as $key => $value){
                if(is_null($value)){
                    $where_arr[] = "`$key` IS NULL";
                }
                else{
                    $safe_val = mysqli_escape_string($conn, $value);
                    $where_arr[] = "`$key` = '$safe_val'";
                }
            }
            
            $sql .= " WHERE " . implode(" AND ", $where_arr);
        }
        return mysqli_query($conn, $sql);
    }
?>