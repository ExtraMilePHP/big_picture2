<?php

class insertCSV
{
    private $filename;
    private $preexist_array;
    private $database_fields;
    private $table_name;
    private $csvType;
    private $arr;
    private $locate_cols;
    private $fill_match = 0;
    private $con;
    private $organizationId;
    private $sessionId;
    private $myThemename;

    public function __construct($filename, $preexist_array, $database_fields, $table_name, $con, $organizationId, $sessionId, $myThemename)
    {
        $this->filename = $filename;
        $this->preexist_array = $preexist_array;
        $this->database_fields = $database_fields;
        $this->table_name = $table_name;
        $this->csvType = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        $this->con = $con;
        $this->organizationId = $organizationId;
        $this->sessionId = $sessionId;
        $this->myThemename = $myThemename;
    }

    public function run() {
        if (in_array($this->filename["type"], $this->csvType)) {
            $this->collectData();
        } else {
            echo "Please use CSV File";
        }
    }

    public function collectData() {
        $this->arr = array(array(), array());
        $num = 0;
        $row = 0;
        $handle = fopen($this->filename['tmp_name'], 'r');

        if ($handle !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $encodedData = array_map([$this, 'encodeSpecialCharacters'], $data);
                $num = count($encodedData);
                for ($c = 0; $c < $num; $c++) {
                    $this->arr[$row][$c] = $encodedData[$c];
                }
                $row++;
            }
            $this->rowAuth();
        }
    }

    public function encodeSpecialCharacters($data) {
        return htmlentities($data, ENT_QUOTES, 'UTF-8');
    }

    public function truncateTable()
    {
        $table_name = $this->table_name;
        $trunc_pre = "TRUNCATE TABLE $table_name";
        if ($this->con->query($trunc_pre)) {
            $this->insertRows();
        } else {
            echo "error in truncate table";
        }
    }

    public function rowAuth()
    {
        $header = $this->arr[0];
        $error_array = array();
        $this->locate_cols = array();
        $match = 0;

        for ($i = 0; $i < count($this->preexist_array); $i++) {
            $pushLocation = array_search($this->preexist_array[$i], $header);

            if ($pushLocation === false) {
                array_push($error_array, $this->preexist_array[$i]);
                $this->locate_cols[$i] = null; // Mark as null if not found
            } else {
                $this->locate_cols[$i] = $pushLocation;
                $match++;
            }
        }

        if ($match > 0) {
            $this->insertRows();
        } else {
            echo "Not matched. You must add: ";
            foreach ($error_array as $error) {
                echo $error . " ";
            }
        }
    }

    public function insertRows()
{
    $myThemename = $this->myThemename;
    $clearField = "DELETE from `" . $this->table_name . "` where themename='$myThemename' and sessionId='".$this->sessionId."' and organizationId = '".$this->organizationId."'";
    $this->con->query($clearField);

    for ($i = 1; $i < count($this->arr); $i++) {
        $sql_values = array();

        for ($l = 0; $l < count($this->preexist_array); $l++) {
            if ($this->locate_cols[$l] !== null) {
                $data = $this->arr[$i][$this->locate_cols[$l]];
                $sql_values[$l] = trim($data);
            } else {
                $sql_values[$l] = "NULL"; // Use NULL if the field is not found
            }
        }

        $insert_values = "'" . implode("','", $sql_values) . "'";
        $organizationId = $this->organizationId;
        $sessionId = $this->sessionId;

        $sql = "INSERT INTO `" . $this->table_name . "` (organizationId, sessionId, themename, " . implode(', ', $this->database_fields) . ") VALUES ('$organizationId','$sessionId','$myThemename'," . $insert_values . ")";

        if ($this->con->query($sql)) {
            $this->fill_match++;
        }
    }

    $this->result();
}

    public function result()
    {
        if ($this->fill_match == count($this->arr) - 1) {
            echo "1010";
        } else {
            echo "Something went wrong: " . mysqli_error($this->con);
        }
    }
}
?>
