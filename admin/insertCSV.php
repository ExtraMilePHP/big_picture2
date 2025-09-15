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
    private $curruntTheme;

    public function __construct($filename, $preexist_array, $database_fields, $table_name, $con, $organizationId, $sessionId, $curruntTheme)
    {
        $this->filename = $filename;
        $this->preexist_array = $preexist_array;
        $this->database_fields = $database_fields;
        $this->table_name = $table_name;
        $this->csvType = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        $this->con = $con;
        $this->organizationId = $organizationId;
        $this->sessionId = $sessionId;
        $this->curruntTheme = $curruntTheme;
    }

    public function run() {
        $this->con->set_charset("utf8mb4"); // Set charset
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
                // Decode HTML entities and ensure UTF-8 encoding
                // $encodedData = array_map(function($value) {
                //     return mb_convert_encoding(html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'UTF-8');
                // }, $data);
                $this->con->set_charset("utf8mb4");

                $encodedData = array_map(function($value) {
                    return mb_convert_encoding(html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'UTF-8');
                }, $data);
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
        // Convert any HTML entities to their respective characters
        $data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
        // Ensure it's properly encoded to UTF-8
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
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
        $curruntTheme = $this->curruntTheme;
        $clearField = "DELETE from `" . $this->table_name . "` where themename='$curruntTheme' and sessionId='".$this->sessionId."' and organizationId = '".$this->organizationId."'";
        $this->con->query($clearField);
    
        for ($i = 1; $i < count($this->arr); $i++) {
            $sql_values = array();
    
            for ($l = 0; $l < count($this->preexist_array); $l++) {
                if ($this->locate_cols[$l] !== null) {
                    $data = $this->arr[$i][$this->locate_cols[$l]];
    
                    // Normalize only the 'category' field
                    if ($this->preexist_array[$l] === "category") {
                        $data = $this->normalizeCategory(trim($data));
                    } else {
                        // Normalize apostrophes for 'question_name' field
                        if ($this->preexist_array[$l] === "question_name") {
                            $data = $this->normalizeApostrophes(trim($data));
                        }
                    }
    
                    // Log data for debugging (check apostrophes)
                    error_log("Data for field " . $this->preexist_array[$l] . ": " . $data);
    
                    $sql_values[$l] = $data;
                } else {
                    $sql_values[$l] = null; // Use NULL for missing values
                }
            }
    
            // Insert query using prepared statements
            $placeholders = implode(',', array_fill(0, count($sql_values), '?'));
            $insert_sql = "INSERT INTO " . $this->table_name . " (organizationId, sessionId, themename, " . implode(', ', $this->database_fields) . ") VALUES (?, ?, ?, " . $placeholders . ")";
            $stmt = $this->con->prepare($insert_sql);
    
            // Bind the parameters dynamically
            $params = array_merge([$this->organizationId, $this->sessionId, $curruntTheme], $sql_values);
            $types = str_repeat('s', count($params)); // Assuming all are strings
            $stmt->bind_param($types, ...$params);
    
            if ($stmt->execute()) {
                $this->fill_match++;
            }
        }
    
        $this->result();
    }
    
    public function normalizeApostrophes($data) {
        $data = str_replace(["’", "`"], "'", $data); // Replace curly and backtick apostrophes
        return $data;
    }


/**
 * Normalize category names to a consistent format.
 * For example, "warmpup", "warm-up", "warm up" -> "Warm-up"
 */
public function normalizeCategory($category)
{
    $category = strtolower($category); // Convert to lowercase for normalization
    $replacements = [
        'warmpup' => 'Warm-up',
        'warm up' => 'Warm-up',
        'warm-up' => 'Warm-up',
    ];

    return $replacements[$category] ?? ucfirst(str_replace(' ', '-', $category));
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