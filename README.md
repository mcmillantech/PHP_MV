# PHP_MV
<h3>Utility to enable separation of PHP and HTML code</h3>
<p>This overcomes the need to keep flipping between PHP and HTML in a development project.</p>
<p>The PHP is contained in a file that works as the model. It constructs all the data and stores in in a multi dimensional array. The HTML is contained in a separate file.</p>
<p>This has the advantages of clarity and that a development tool like NetBeans provides syntax check and auto completion for both languages.</p>
<p>The HTML file contains pseudo elements that act as placeholders for the data values to be displayed.  For example <{amount}>.</p>
<p>To use the utility, each PHP file must include the file <i>view.php</i>.  A single call to function <i>showView</i> holds the path to the HTML file and the array holding the data values.  See an example in files test.php and test.html.</p>
