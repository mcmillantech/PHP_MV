# PHP_MV
Utility to enable separation of PHP and HTML code
This overcomes the need to keep flipping between PHP and HTML in a development project.
The PHP is contained in a file that works as the model. It constructs all the data and stores in in a multi dimensional array. The HTML is contained in a separate file.
This has the advantages of clarity and that a development took like NetBeans provides syntax check and auto completion for both languages.
The HTML file contains pseudo elements that act as placeholders for the data values to be displayed.  For example <{amount}>.
To use the utility, each PHP file must include the file view.php.  A single call to function showView holds the path to the HTML file and the array holding the data values.  See an example in files test.php and test.html.
