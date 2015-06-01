This script attempts to take a book from booki and turn it into series of markdown files and images in a directory structure that gitbook would expect.

#Installation and usage

1. Install gitbook-cli (via npm https://github.com/GitbookIO/gitbook-cli) and pandoc (http://pandoc.org/installing.html)
2. Use wget to download the source for a book from booki, for example for CiviCRM do
```
cd [directory-you-want-to-put-the-source-in]
wget -E -H -k -p http://booki.flossmanuals.net/civicrm/_full/
```
3. Create a config.php file (using config.example.php as a starting point) and make appropriate changes to the variables
4. Run the process.php command ('php booki2gitbook/process.php') and see what happens.
5. If all goes to plan, you will see a list of directory names outputted to the screen and a gitbook will be created in the destination directory you specified in the config.

**Note**: Most likely things will not go to plan the first time and you will have to edit you booki, redownload the source code (step 2) and run process.php again.  Some of the things I had to change include

* Removing funny characters from the the chapter names (the ones that appear at the top of chapters, not the ones in the index).
* Removing any empty H2 tags in the output
* There are likely many other things you might have to change - depending on the state of your manual
