<?php

function PPAP () {

$img = new Imagick('ppap_template.jpg');
$draw = new ImagickDraw();

header("Content-Type: image/jpg");

$draw->setFillColor('white');
$draw->setFont("fonts/Ubuntu-R.ttf");
$draw->setFontSize(50);
$draw->setStrokeColor('#000');
$draw->setStrokeWidth(2);
$draw->setStrokeAntialias(true);
$draw->setTextAntialias(true);
$draw->setTextAlignment(Imagick::ALIGN_CENTER);

$word1 = $_POST["t1"];
$word2 = $_POST["t2"];
$word3 = $_POST["t3"];
$word4 = $_POST["t4"];

//$text = drawText($word);

/*
list($lines, $lineHeight) = wordWrapAnnotation($img, $draw, $word1, 480);
for ($i = 0; $i < count($lines); $i++) {
    $img->annotateImage($draw,250,300 + $i * $lineHeight, 0, $lines[$i]);
}
*/

placeText($img, $draw, $word1, 390);
placeText($img, $draw, $word2, 740);
placeText($img, $draw, $word3, 1085);
placeText($img, $draw, $word4, 1435);

date_default_timezone_set("Asia/Taipei");
$t = time();
$d = date("Y-m-d h:i:sa",$t);

file_put_contents('ppap_log', $_SERVER['REMOTE_ADDR'] . PHP_EOL, FILE_APPEND);
file_put_contents('ppap_log', $_SERVER['HTTP_USER_AGENT'] . PHP_EOL, FILE_APPEND);
file_put_contents('ppap_log', $d . PHP_EOL, FILE_APPEND);
file_put_contents('ppap_log', $word1 . PHP_EOL, FILE_APPEND);
file_put_contents('ppap_log', $word2 . PHP_EOL, FILE_APPEND);
file_put_contents('ppap_log', $word3 . PHP_EOL, FILE_APPEND);
file_put_contents('ppap_log', $word4 . PHP_EOL, FILE_APPEND);

//$img->compositeImage($text, imagick::COMPOSITE_OVER,0,300,Imagick::CHANNEL_ALPHA);

echo $img->getImageBlob();

}

function placeText (&$image, &$draw, $word, $ypos) {
    list($lines, $lineHeight) = wordWrapAnnotation($image, $draw, $word, 480 /* Width */);
    $ypos -= count($lines) * $lineHeight;
    for ($i = 0; $i < count($lines); $i++) {
        $image->annotateImage($draw, 250, $ypos + $i * $lineHeight, 0, $lines[$i]);
    }
}

function drawText ($text) {

    $draw = new \ImagickDraw();
    $draw->setStrokeColor('black');
    $draw->setFillColor('white');
    $draw->setStrokeWidth(1);
    $draw->setFontSize(72);
    $draw->setFont("fonts/Ubuntu-R.ttf");
    $draw->setStrokeAntialias(true);
    $draw->setTextAntialias(true);

    $draw->setTextAlignment(\Imagick::ALIGN_CENTER);
    $draw->annotation(250, 50, $text);

    $imagick = new \Imagick();
    $imagick->newImage(500, 100, 'transparent');
    $imagick->setImageFormat("png");
    $imagick->drawImage($draw);

    header("Content-Type: image/png");
    return $imagick;

}

function wordWrapAnnotation(&$image, &$draw, $text, $maxWidth)
{
    $words = explode(" ", $text);
    $lines = array();
    $i = 0;
    $lineHeight = 0;
    while($i < count($words) )
    {
        $currentLine = $words[$i];
        if($i+1 >= count($words))
        {
            $lines[] = $currentLine;
            if (count($words) == 1) {
                $metrics = $image->queryFontMetrics($draw, $currentLine . ' ' . $words[$i+1]);
                $lineHeight = $metrics['textHeight'];
           }
            
            
            break;
        }
        //Check to see if we can add another word to this line
        $metrics = $image->queryFontMetrics($draw, $currentLine . ' ' . $words[$i+1]);
        while($metrics['textWidth'] <= $maxWidth)
        {
            //If so, do it and keep doing it!
            $currentLine .= ' ' . $words[++$i];
            if($i+1 >= count($words))
                break;
            $metrics = $image->queryFontMetrics($draw, $currentLine . ' ' . $words[$i+1]);
        }
        //We can't add the next word to this line, so loop to the next line
        $lines[] = $currentLine;
        $i++;
        //Finally, update line height
        if($metrics['textHeight'] > $lineHeight)
            $lineHeight = $metrics['textHeight'];
    }
    return array($lines, $lineHeight);
}

PPAP();

?>
