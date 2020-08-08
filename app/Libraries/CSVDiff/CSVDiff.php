<?php

namespace App\Libraries\CSVDiff;

use function Ramsey\Uuid\v1;

class CSVDiff {

    const UNCHANGED  = 0;
    const REMOVED    = 1;
    const ADDED      = 2;
    const UPDATED    = 3;

    private $from_file_path = '';
    private $to_file_path = '';

    /**
	* Constructor
	* ...
	*/
	public function __construct($from_file_path , $to_file_path) {
        $this->from_file_path = $from_file_path;
        $this->to_file_path = $to_file_path;
    }

    /*
    * Return an opcodes string describing the diff between a "From" and a
	* "To" string
	*/
	public static function getDiffFromFiles($from_file_path , $to_file_path) {
		$diff = new CSVDiff($from_file_path, $to_file_path);
		return $diff->getDiff();
    }

    public static function getHtmlFromDiff($diff) {
    }

    public function getDiff()     
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

        while(($f1_line = fgets($handleF1)) !== FALSE && ($f2_line = fgets($handleF2)) !== FALSE) {
            //Is F1 Line == F2 Line
            if (strcmp($f1_line, $f2_line) == 0) {
                //Mark Unchanged
                echo "<p style='background-color:white;'>". $f2_line. "</p>";
                continue;
            } 

            //TODO: dynamic 
            //Are they 80% Similiar
            similar_text($f1_line, $f2_line, $percentage);

            if ($percentage > 80) {
                //Mark Updated
                echo "<p style='background-color:blue;'>". $f2_line. "</p>";
                continue;
}

            //Does F1 Line exists F2 File?
            if (($pos = $this->lineExist($f1_line, $handleF2, ftell($handleF2))) == -1) {
                //Mark Removed
                echo "<p style='background-color:red;'>". $f1_line. "</p>";
                //Only move F1 Pointer, so go back in F2
                fseek($handleF2, ftell($handleF2) - strlen($f2_line));
                continue;
            }

            // Do Previous Lines Exist in F1?
            $previousLinesExists = FALSE;
            // Get previous Lines 
            while(ftell($handleF2) < $pos) {
                if (($pos = $this->lineExist($f2_line, $handleF1)) != -1) {
                    $previousLinesExists = TRUE;
                    break;
                } 
            } 
            
            if($previousLinesExists) {
                //Mark Removed
                echo "<p style='background-color:orange;'>". $f2_line. "</p>";
                //Only move F1 Pointer, so go back in F2
                fseek($handleF2, ftell($handleF2) - strlen($f2_line));
            } else {
                //Mark as added
                echo "<p style='background-color:green;'>". $f2_line. "</p>";
                fseek($handleF1, ftell($handleF1) - strlen($f1_line));
            }
        }

        //Remove All 
        while(($f1_line = fgets($handleF1)) !== FALSE) {
            //Mark as removed
            echo "<p style='background-color:red;'>-". $f1_line. "</p>";
        }
        //Add All
        while(($f2_line = fgets($handleF2)) !== FALSE) {
            //Mark as added
            echo "<p style='background-color:green;'>-". $f2_line. "</p>";
        }
        
        //Close Files
        fclose($handleF1);
        fclose($handleF2);
    }

    /*
    *
    * returns offset if exists -1 if not
    */
    private function lineExist($str, $handler, $offset = 0) {
        $previousOffset = ftell($handler);
        $lineExists = FALSE;

        while (($line = fgets($handler)) !== FALSE) {
            if(strcmp($str, $line) == 0) {
                $lineExists = TRUE;
                break;
            }
        }

        $currentOffset = ftell($handler) - strlen($line);
        fseek($handler, $previousOffset);
        return $lineExists == FALSE ? -1 : $currentOffset;
    }

}
