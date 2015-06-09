<?php
if(file_exists('config.php')){
    include_once('config.php');
} else {
    die("Could not find config.php. Exiting...\n");
}
//Before using this script, you'll want to get a local copy of the manual that you want to export
//You should do this with wget on a URL such as http://booki.flossmanuals.net/civicrm/_full/
//
//Something like the following (in an appropriate directory) should do the trick.

//wget -E -H -k -p http://booki.flossmanuals.net/civicrm/_full/ 

//Then define the three config variables below.

//If anything isn't clear email michaelmcandrew@thirdsectordesign.org and I'll try to help



//There should be no need to change anything below this line,
$partDirectory = '';
$lastPartDirectory = '';
$partTitle = '';
$lastPartTitle = '';
$partContents = '';
$chapterDelimiter = '<h1>';


//Load up the source
$html = file_get_contents("{$sourceDirectory}index.html");

//Prepare (empty) the destination directory
system ('rm -r '.$destinationDirectory.'*');
system ('mkdir '.$destinationDirectory.'images');

//Copy all images files into new location
$extensions = array('png', 'PNG', 'jpg', 'JPG', 'gif');
foreach($extensions as $extension){
    system("cp {$sourceDirectory}static/*.{$extension} {$destinationDirectory}images/");
}

$chapters = explode($chapterDelimiter, $html);


foreach ($chapters as $chapter){
    $chapterContents = trim($chapterDelimiter.$chapter);
    preg_match('/<h1>(.*)<\/h1>/s', $chapterContents, $matches);

    manipulateHTML($chapterContents);
    //If this section has an h1 in it (should always be true apart from the first array item)
    if(count($matches)){
        $chapterTitle = trim($matches[1]);
	echo $chapterTitle."\n";
        $chapterFilename = strtolower(str_replace(array(' ', '/', '?', '(', ')', ','), array('-'), $chapterTitle));
        file_put_contents($destinationDirectory.$partDirectory.$chapterFilename.'.html', $chapterContents);
        $partMD[] ="   * [{$chapterTitle}](./{$chapterFilename}.md)";
        $summary[] = "   * [{$chapterTitle}](/{$partDirectory}{$chapterFilename}.md)";
        system("pandoc -f html -t markdown {$destinationDirectory}{$partDirectory}{$chapterFilename}.html -o {$destinationDirectory}{$partDirectory}{$chapterFilename}.md");

        $md = file_get_contents("{$destinationDirectory}{$partDirectory}{$chapterFilename}.md");
        manipulateMD($md);
        file_put_contents("{$destinationDirectory}{$partDirectory}{$chapterFilename}.md", $md);

        //If the last line of this file is an h2, interpret this as the start of a new section.
        $lines = file("{$destinationDirectory}{$partDirectory}{$chapterFilename}.html");
        $lastLine = $lines[count($lines)-1];
        preg_match('/<h2>(.*)<\/h2>$/s', $lastLine, $matches);
        if(count($matches)){

            // TODO Remove the last line from the chapter

            $partTitle = trim($matches[1]);
            $partDirectory = str_replace(array(' ', '/', '?', '(', ')', ','), array('-'), strtolower(trim($matches[1]))).'/';
            system("mkdir {$destinationDirectory}{$partDirectory}");
            $partContents="# {$lastPartTitle}\n".implode("\n",$partMD);
            file_put_contents($destinationDirectory.$lastPartDirectory.'README.md', $partContents);
            $lastPartDirectory=$partDirectory;
            $lastPartTitle=$partTitle;
            $summary[]="* [{$partTitle}](/{$partDirectory}README.md)";
            $readme[]="* [{$partTitle}](/{$partDirectory}README.md)";
            unset($partMD);
        }
    }
}

system("rm {$destinationDirectory}*.html");
system("rm {$destinationDirectory}*/*.html");
//Remove the first and last element from the summary since these are artifacts of the migration and not real chapters.
unset($summary[0]);
array_pop($summary);


file_put_contents($destinationDirectory.'SUMMARY.md', implode("\n",$summary));
file_put_contents($destinationDirectory.'README.md', "# {$manualName}\n".implode("\n",$readme));
system("gitbook init {$destinationDirectory}");


function manipulateHTML(&$contents){
    $contents = str_replace('src="static/', 'src="/images/', $contents);
}

function manipulateMD(&$contents){
    $contents = str_replace('\\', '', $contents);
}
