<?php
// ------------------------------------------------------
//  Project	Simple model / view
//  File	View.php
//  		Populates a view from array data
//
//  Author	John McMillan, McMillan Technolo0gy
//
//  Version	1.0.2
// ------------------------------------------------------

// ---------  Sample use -------------------
// - The php file

/*
    $dta = array (
        "title" => "Test the view",
        "details" => "Here be details",
        "line2" => "This should be bold"
    );
    $list = array();		// Create a sub array for a list
    $line = array();

        $line["line"] = "Line 1";
        $line["data"] = "data 1";
        array_push($list, $line);
        $line["line"] = "Line 2";
        $line["data"] = "Mersey";
        array_push($list, $line);

    $ar["list"] = $list;
    showView("tst.html", dta);	

------ The html file
<div>
	<h1><{title}></h1>
	Test for <{details}><br>
	<b><{line2}></b><br>
	End<br>
	<{list}>
		LIst item <{line}> value <{data}><br>
	<{/list}><br>
	<br>After the loop<br>
</div>

*/

define('DEBUG', FALSE);

// -----------------------------------------------
// The View class which implements the scheme
// 
// -----------------------------------------------
class View
{
    private $inputStream;
    private $outputStream;
    private $dta;
    private $streamPsn = 0;
    
    const elementOpen = '<{';
    const elementClose = '}>';
    const elnChars = 2;


    // -----------------------------------------------
    // The constructor
    // 
    // Parameters   HTML input stream
    //              Array from PHP script
    // -----------------------------------------------
    function __construct($pstream, $dta) {
        $this->inputStream = $pstream;
        $this->dta = $dta;
    }
	
    // -----------------------------------------------
    // The main call point
    // -----------------------------------------------
    public function go() {
        $this->processStream($this->inputStream, $this->dta);
        echo $this->outputStream;
    }
	
    // -----------------------------------------------
    // Scan the html document, and build in the
    // values from the model
    // -----------------------------------------------
    private function processStream($stream, $dta)
    {
        $this->streamPsn = 0;		// Set the next byte to inspect
                                        // Loop through the input stream
        do {
                                        // Find the next pseudo element
            $pElement = $this->findNextElement($stream, $this->streamPsn);
            if ($pElement === FALSE) {
                    	// No more pseudo elements - output to the end of file
                $str = substr($stream, $this->streamPsn);
                $this->outputStream .= $str;
                break;
            }
            $this->streamPsn = $this->processElement($pElement, $stream, $dta);
            if (DEBUG) {
                echo " $pElement, $this->streamPsn ";
            }
        } while (TRUE);
    }

    // -----------------------------------------------
    //	Locate next pseudo element
    //
    //	Step over comments, styles and scripts
    //
    //	Parameter input stream
    //
    //	Returns	position of next element
    // -----------------------------------------------
    private function findNextElement($stream)
    {

        $html = FALSE;
        $localStrPsn = $this->streamPsn;
        
        while (!$html) {
                                    // Find the start of pseudo element
            $elPsn = strpos($stream, self::elementOpen, $localStrPsn);
            if ($elPsn === FALSE)			// No more elements
                return FALSE;
            if (DEBUG) {
                $trace = substr($stream, $elPsn, 16);
                echo " Next el $trace~<br>";
            }
                            // Look for comments in forward stream
            $commentPsn = strpos($stream, "<!--", $localStrPsn );
            if ($commentPsn && ($commentPsn < $elPsn)) {
                                                // Comment is present
                $localStrPsn = strpos($stream, "-->", $commentPsn);
                if ($localStrPsn === FALSE)	// Unterminated comment
                        return FALSE;           // ... Echo the rest of the stream
                $localStrPsn += 3;
                continue;			// Skip over the comment
            }
            $html = TRUE;
        }

        return $elPsn;
    }
	
    // -----------------------------------------------
    //  Process an element
    //  
    //  Finds the name(?) of the element, takes the 
    //  value from the model and outputs it
    //  
    //  Parameters  Pointer to the element
    //              Start of the text stream before it
    //              Data values from the model
    //
    //  Returns     Pointer to stream after the element
    // -----------------------------------------------
    private function processElement($pToken, $stream, $dta)
    {
                        // Output the fixed text from the last element
        $strLen = $pToken - $this->streamPsn;
        $str = substr($stream, $this->streamPsn, $strLen);
        $this->outputStream .= $str;

                            // Set $token to the element name 
        $pTkEnd = strpos($stream, self::elementClose, $pToken);
        $len = $pTkEnd - $pToken - self::elnChars;
        $token = substr($stream, $pToken+self::elnChars, $len);

        $newPtr = $pTkEnd + self::elnChars;
        if (DEBUG){
            echo "token $token ";
        }
        if (!array_key_exists($token, $dta))
            echo ("View error: element $token not found in data array");
        if (is_array($dta[$token]) ) {
            $newPtr = $this->doLoop($token, $stream, $newPtr);
            return $newPtr;
        }
        $this->outputStream .= $dta[$token];

        return $newPtr;
    }

    // -----------------------------------------------
    // Process a list element
    // 
    // -----------------------------------------------
    private function doLoop($token, $stream, $startPtr)
    {
                            // Locate end of the loop in the html
        $endToken = self::elementOpen . "/$token}";
        $endOfLoop = strpos($stream, $endToken, $startPtr);
        if ($endOfLoop === FALSE) {
            die ("End token $endToken not found");
        }

        $lengthOfStream = $endOfLoop - $startPtr;
        $thisStream = substr($stream, $startPtr, $lengthOfStream);

        foreach ($this->dta[$token] as $loopAr) {
            $this->processStream($thisStream, $loopAr);
        }
        $newPtr = $endOfLoop + strlen($endToken) + 1;
        return $newPtr;
    }
    
} // End of class


// --------------------------------------------
//  Calling point for the scheme
//  
//  Merge the model data into the view template
//
//  When complete, echo the merged html
// --------------------------------------------
function showView($viewFile, $dta)
{
	$hFile = fopen($viewFile, 'r')
		or die ("No file $viewFile");
	$source = fread($hFile, filesize($viewFile));
	fclose($hFile);
        
	$view = new View($source, $dta);
	$view->go();

	return;
}

?>
