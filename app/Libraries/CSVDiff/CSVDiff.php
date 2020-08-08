<?php

namespace App\Libraries\CSVDiff;

abstract class DiffEnum
{
    const UNCHANGED  = 0;
    const REMOVED    = 1;
    const ADDED      = 2;
    const UPDATED    = 3;
}

class CSVDiff
{
    //MARK: - Constants -
    const LINE_KEY      = "Line";
    const STATYS_KEY    = "Status";
    const DIFF_KEY      = "Difference";

    const OLD_KEY      = "Old";
    const NEW_KEY      = "New";

    //MARK: - Variables - 
    private $from_file_path = '';
    private $to_file_path   = '';
    private $diffArray      = array();

    //MARK: - Constructors - 

    /**
     * Constructor
     * ...
     */
    public function __construct($from_file_path, $to_file_path)
    {
        $this->from_file_path = $from_file_path;
        $this->to_file_path = $to_file_path;
    }

    //MARK: - Static Methods -

    /*
    * Return an Array string describing the diff between a "From" and a "To" string
    *  ...
	*/
    public static function getDiffFromFiles($from_file_path, $to_file_path)
    {
        $diff = new CSVDiff($from_file_path, $to_file_path);
        return $diff->getDiff();
    }

    public static function getHtmlFromDiff($diff)
    {
        $i = 0;
        $addedColor = "#66bb6a";
        $removedColor = "#ff3d00";
        $updatedColor = "#78909c";
        $defaultColor = "white";

        echo '
            <div style="background-color:' . $addedColor . '" width="150"> New Line </div>
            <div style="background-color:' . $removedColor . '" width="150"> Deleted Line </div>
            <div style="background-color:' . $updatedColor . '" width="150"> Updated Line </div>
        ';

        //Open Table
        echo '<table style="width:100%"> ';
        $header = explode(";", $diff[0][CSVDiff::LINE_KEY]);

        echo "<tr style='background-color:grey'>";
        echo "<th style='border-bottom: 1px solid #ddd'> # </th>";
        foreach ($header as $columName) {
            echo "<th style='border-bottom: 1px solid #ddd'> $columName </th>";
        }
        echo "</tr>";

        array_shift($diff);
        foreach ($diff as $line) {
            $pColor = "";
            switch ($line[CSVDiff::STATYS_KEY]) {
                case DiffEnum::ADDED:
                    $pColor = $addedColor;
                    break;
                case DiffEnum::REMOVED:
                    $pColor = $removedColor;
                    break;
                case DiffEnum::UNCHANGED:
                    $pColor = $defaultColor;
                    break;
                case DiffEnum::UPDATED:
                    $pColor = $updatedColor;
                    break;

                default:
                    $pColor = $defaultColor;
            }

            $line = explode(";", $line[CSVDiff::LINE_KEY]);
            echo "<tr style='background-color:$pColor'>";

            echo "<td style='border-bottom: 1px solid #ddd'>" . ++$i . "</td>";

            foreach ($line as $rowField) {
                echo "<td style='border-bottom: 1px solid #ddd'> $rowField </td>";
            }
            echo "</tr>";
        }

        //Close Table
        echo "</table>";
    }

    public static function getJsonFromDiff($diff)
    {
        return json_encode($diff);
    }

    //MARK: - Private Methods -

    private function getDiff()
    {
        //Open Files
        $handleF1 = fopen($this->from_file_path, "r");
        $handleF2 = fopen($this->to_file_path, "r");

        //Check if we were able to open files
        if ($handleF1 === FALSE || $handleF2 === FALSE) {
            $handleF1 !== FALSE ? fclose($handleF1) : "";
            $handleF2 !== FALSE ? fclose($handleF2) : "";
            //TODO: think about exceptions?
            return "";
        }

        while (($f1_line = fgets($handleF1)) !== FALSE && ($f2_line = fgets($handleF2)) !== FALSE) {
            //Is F1 Line == F2 Line
            if (strcmp($f1_line, $f2_line) == 0) {
                //Mark Unchanged
                $this->markLine($f2_line, DiffEnum::UNCHANGED);
                continue;
            }

            //Are they 80% Similiar
            similar_text($f1_line, $f2_line, $percentage);

            if ($percentage > 80) {
                //Mark Updated
                $highlight = $this->highlightDiff($f1_line, $f2_line);
                $this->markLine($f2_line, DiffEnum::UPDATED, $highlight[CSVDiff::NEW_KEY]);
                continue;
            }

            //Does F1 Line exists F2 File?
            if (($pos = $this->lineExist($f1_line, $handleF2, ftell($handleF2))) == -1) {
                //Mark Removed
                $this->markLine($f2_line, DiffEnum::REMOVED);
                //Only move F1 Pointer, so go back in F2
                fseek($handleF2, ftell($handleF2) - strlen($f2_line));
                continue;
            }

            // Do Previous Lines Exist in F1?
            $previousLinesExists = FALSE;
            // Get previous Lines 
            while (ftell($handleF2) < $pos) {
                if (($pos = $this->lineExist($f2_line, $handleF1)) != -1) {
                    $previousLinesExists = TRUE;
                    break;
                }
            }

            if ($previousLinesExists) {
                //Mark Removed
                $this->markLine($f2_line, DiffEnum::REMOVED);
                //Only move F1 Pointer, so go back in F2
                fseek($handleF2, ftell($handleF2) - strlen($f2_line));
            } else {
                //Mark as added
                $this->markLine($f2_line, DiffEnum::ADDED);
                fseek($handleF1, ftell($handleF1) - strlen($f1_line));
            }
        }

        //Remove All 
        while (($f1_line = fgets($handleF1)) !== FALSE) {
            //Mark as removed
            $this->markLine($f1_line, DiffEnum::REMOVED);
        }
        //Add All
        while (($f2_line = fgets($handleF2)) !== FALSE) {
            //Mark as added
            $this->markLine($f2_line, DiffEnum::ADDED);
        }

        //Close Files
        fclose($handleF1);
        fclose($handleF2);

        //Return diffArray
        return $this->diffArray;
    }

    /*
    *
    * returns offset if exists -1 if not
    */
    private function lineExist($str, $handler)
    {
        $previousOffset = ftell($handler);
        $lineExists = FALSE;

        while (($line = fgets($handler)) !== FALSE) {
            if (strcmp($str, $line) == 0) {
                $lineExists = TRUE;
                break;
            }
        }

        $currentOffset = ftell($handler) - strlen($line);
        fseek($handler, $previousOffset);
        return $lineExists == FALSE ? -1 : $currentOffset;
    }

    private function markLine($str, $enum, $difference = null)
    {

        if (!is_numeric($enum)) {
            return;
        }

        $array = array(CSVDiff::LINE_KEY => $str, CSVDiff::STATYS_KEY => $enum);

        if ($difference != null) {
            $array[CSVDiff::DIFF_KEY] = $difference;
        }

        array_push($this->diffArray, $array);
    }

    private function highlightDiff($old, $new){
        $from_start = strspn($old ^ $new, "\0");        
        $from_end = strspn(strrev($old) ^ strrev($new), "\0");
    
        $old_end = strlen($old) - $from_end;
        $new_end = strlen($new) - $from_end;
    
        $start = substr($new, 0, $from_start);
        $end = substr($new, $new_end);
        $new_diff = substr($new, $from_start, $new_end - $from_start);  
        $old_diff = substr($old, $from_start, $old_end - $from_start);
    
        $new = "$start{{$new_diff}}$end";
        $old = "$start{{$old_diff}}$end";

        return array(CSVDiff::OLD_KEY=>$old, CSVDiff::NEW_KEY=>$new);
    }
}
