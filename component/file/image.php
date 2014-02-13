<?php


class file_image extends component {

    const NULL_FILE = -1;
    const BAD_FILE = -2;

    const SIZE__TINY = 1;
    const SIZE__SMALL = 2;
    const SIZE__NORMAL = 3;

    const MAX_RATIO = 2.0;
    
    
    var $_police = array(
        'Douds Cap Light' => 'doudscaplight.ttf',
        'Douds light' => 'doudslight.ttf',
        'Roboto Regular' => 'Roboto-Regular.ttf',
        'Arial' => 'arial.ttf',
        'Comic Sans MS' => 'comic.ttf',
        'Courier New' =>'cour.ttf',
        'Tahoma' =>'tahoma.ttf',
        'Times New Roman' => 'times.ttf',
        'Verdana'=>'verdana.ttf'
        );

    public function hex2rgb($color) {
        if (strlen($color) > 1)
            if ($color[0] == '#')
                $color = substr($color, 1);

            if (strlen($color) == 6)
                list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
            elseif (strlen($color) == 3)
                list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
            else
                return false;

            return array(hexdec($r), hexdec($g), hexdec($b));
        }

    public function generateFromImgWithLabel($path, $labels, $field) {

        $image_source = @imagecreatefrompng($path);
        if (!$image_source) {
            $image_source = @imagecreatefromgif($path);
        }
        if (!$image_source) {
            $image_source = @imagecreatefromjpeg($path);
        }
        if (!$image_source) {
            $image_source = @imagecreatefromwbmp($path);
        }
        if (!$image_source) {
            return false;
        }


        $size = getimagesize($path);
        $new_image = imagecreatetruecolor($size[0],$size[1]); 
        $transparencyIndex = imagecolortransparent($image_source);
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

        if ($transparencyIndex >= 0) {
            $transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);   
        }

        $transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
        imagefill($new_image, 0, 0, $transparencyIndex);
        imagecolortransparent($new_image, $transparencyIndex);


        imagecopyresampled($new_image,$image_source,0,0,0,0,$size[0],$size[1],$size[0],$size[1]);


        foreach ($labels as &$label) {

            $font = $this -> application -> path . 'font/' . $this -> _police[$label['font']];
            
            $lines = array();
            

            $cc = imageftbbox ( $label['size'] , 0 , $font , $label[$field]);
            
            $lentgh = strlen($label[$field]);
            $fontWidth = ($cc[0]+$cc[2])/$lentgh;


            $fontHeight = -($cc[0]+$cc[5]);


            $maxCharByLine = intval($label['width']/$fontWidth);

            if($maxCharByLine<1){
             $maxCharByLine=1;
         }

         while (strlen($label[$field]) > 0) {

          $lines[] = substr($label[$field], 0, $maxCharByLine);
          $label[$field] = trim(substr($label[$field], $maxCharByLine));

      }


      $rgb = $this -> hex2rgb($label['color']);

      $color = imagecolorclosest($new_image, $rgb[0], $rgb[1], $rgb[2]);
          //  $color = imagecolorexact($img, $rgb[0], $rgb[1], $rgb[2]);



      foreach ($lines as $linePos => $lineContent) {


        imagettftext($new_image, $label['size'], 0, $label['x'], $label['y']+($fontHeight*$linePos)+$fontHeight , $color, $font, $lineContent);


    }





}




return $new_image;
}


public function saveContentTo($fileContent,$sFormat,$destination,$folder,$subfolder,$name){

   $target_path = $this->application->content.$destination;
   $target_path .= DIRECTORY_SEPARATOR . $folder; 
   $target_path .= DIRECTORY_SEPARATOR . $subfolder;


  $file = $this->core->component("file");

   $file->mkdir( $target_path, true);


   $sFileName =  $name.'.'.$sFormat; 

   if(file_exists($target_path.DIRECTORY_SEPARATOR.$sFileName)){
   unlink($target_path.DIRECTORY_SEPARATOR.$sFileName);
}

if(file_put_contents($target_path.DIRECTORY_SEPARATOR.$sFileName, $fileContent)) {

    return array($destination.'.'.$this->application->vhost.DIRECTORY_SEPARATOR . $folder.DIRECTORY_SEPARATOR . $subfolder.DIRECTORY_SEPARATOR.$sFileName,$target_path.DIRECTORY_SEPARATOR.$sFileName) ;

} 

return false;
}

    /*
     static public function createPictures($path, $class, $id, $sizes, $resize = true)
     {
     try
     {
     global $_CONF;

     //       if(strpos($k[1],"jpg")===0 || strpos($k[1],"jpeg")===0) {
     $img = @imagecreatefromjpeg($path);
     if (!$img)
     {
     $img = @imagecreatefromgif($path);
     }
     if (!$img)
     {
     $img = @imagecreatefrompng($path);
     }
     if (!$img)
     {
     $img = @imagecreatefromwbmp($path);
     }
     if (!$img)
     {
     return false;
     }

     $width = imagesx($img);
     $height = imagesy($img);
     if (!$width || !$height)
     {
     return false;
     }

     $ratio = (float)$height/$width;

     foreach ($sizes as $size)
     {

     $goalWidth = $size;
     $goalHeight = $size*Picture::MAX_RATIO;

     if (!$resize)
     {

     $goalWidth = $width;
     $goalHeight = $height;

     } else if ($ratio <= Picture::MAX_RATIO && $width > $goalWidth)
     {
     $goalHeight = intval($ratio*$goalWidth);
     } elseif ($ratio > Picture::MAX_RATIO && $height > $goalHeight)
     {
     $goalWidth = intval($goalHeight/$ratio);
     } else
     {
     $goalWidth = $width;
     $goalHeight = $height;
     }
     $newImg = imagecreatetruecolor($goalWidth, $goalHeight) or die ("unable to imagecreatetruecolor");
     //imagecopyresized($newImg, $img, 0, 0, 0, 0, $goalWidth, $goalHeight, $width, $height) or die("unable to imagecopyresized");
     imagecopyresampled($newImg, $img, 0, 0, 0, 0, $goalWidth, $goalHeight, $width, $height) or die ("unable to imagecopyresized");

     $key = $class."_".$size."_".$id.".jpg";

     $tmpFile = $mogileFS->getTempFile();
     imagejpeg($newImg, $tmpFile,100);
     $mogileFS->set($key, file_get_contents($tmpFile));
     $mogileFS->rmTempFile();

     if($mogileFS->exists($key)){
     bLog($key." picture created");

     }else{

     bLog($key." picture error");
     }

     }

     }

     catch(Exception $e)
     {
     throw new Exception("Exception while creating picture", var_export($e));
     return false;
     }

     }

     return true;

     }
     public static function createPicturesMagickWand($path, $class, $id, $sizes)
     {
     global $_CONF;

     if ($path == null)
     {
     throw new Exception("File is null", self::NULL_FILE);
     }
     // if (!isset($path['tmp_name'])) {
     //  throw new Exception("Invalid ressource", self::BAD_FILE);
     // }

     $img = NewMagickWand();

     if (!MagickReadImage($img, $path))
     {
     throw new Exception("Invalid ressource", self::BAD_FILE);
     }

     # vérifie la hauteur du fichier
     $width = (int)MagickGetImageWidth($img);
     $height = (int)MagickGetImageHeight($img);

     $dims = array (
     array ('width'=>50, 'height'=>50*Picture::MAX_RATIO, 'size'=>'T'),
     array ('width'=>100, 'height'=>100*Picture::MAX_RATIO, 'size'=>'S'),
     array ('width'=>210, 'height'=>230*Picture::MAX_RATIO, 'size'=>'N')
     );

     $ratio = (float)$height/$width;

     foreach ($sizes as $size)
     {
     $goalWidth = $size;
     $goalHeight = $size*Picture::MAX_RATIO;

     $new = CloneMagickWand($img);

     if ($ratio <= Picture::MAX_RATIO && $width > $goalWidth)
     {
     $x = $goalWidth*$ratio;
     MagickScaleImage($new, $goalWidth, $goalWidth*$ratio);
     } elseif ($ratio > Picture::MAX_RATIO && $height > $goalHeight)
     {
     $x = $goalHeight/$ratio;
     MagickScaleImage($new, $goalHeight/$ratio, $goalHeight);
     }

     @mkdir($_CONF['PICTURE_PATH'].DIRECTORY_SEPARATOR.$class.DIRECTORY_SEPARATOR.$size.DIRECTORY_SEPARATOR, 0755, true);
     $path = $_CONF['PICTURE_PATH'].DIRECTORY_SEPARATOR.$class.DIRECTORY_SEPARATOR.$size.DIRECTORY_SEPARATOR.$id.'.jpg';
     bLog($path);
     MagickWriteImage($new, $path);
     }
     return true;
     }

     public function removePictures($class, $id, $sizes)
     {
     global $_CONF;

     foreach ($sizes as $size)
     {
     $path = $_CONF['PICTURE_PATH'].DIRECTORY_SEPARATOR.$class.DIRECTORY_SEPARATOR.$size.DIRECTORY_SEPARATOR.$id.'.jpg';
     if (file_exists($path))
     {
     unlink($path);
     }
     }
     }
     /*
     *
     *  if(!preg_match('/\.(jpg|gif|png)$/', $name)) continue;
     $size = filesize($dir.$name);
     $lastmod = filemtime($dir.$name)*1000;
     $images[] = array('name'=>$name, 'size'=>$size,
     'lastmod'=>$lastmod, 'url'=>$dir.$name);
     *
     */

}
