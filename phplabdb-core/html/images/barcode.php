<?php
 define("BCS_BORDER", 1);
 define("BCS_TRANSPARENT", 2);
 define("BCS_ALIGN_CENTER", 4);
 define("BCS_ALIGN_LEFT", 8);
 define("BCS_ALIGN_RIGHT", 16);
 define("BCS_REVERSE_COLOR", 32);
 define("BCS_DRAW_TEXT", 64);
 define("BCS_STRETCH_TEXT", 128);
 define("BCD_DEFAULT_BACKGROUND_COLOR", 0xFFFFFF);
 define("BCD_DEFAULT_FOREGROUND_COLOR", 0x000000);
 define("BCD_DEFAULT_STYLE", BCS_DRAW_TEXT | BCS_STRETCH_TEXT | BCS_ALIGN_CENTER | BCS_TRANSPARENT);
 define("BCD_DEFAULT_WIDTH", 125);
 define("BCD_DEFAULT_HEIGHT", 50);
 define("BCD_DEFAULT_FONT", 3);
 define("BCD_DEFAULT_XRES", 2);
 define("BCD_DEFAULT_MAR_Y1", 10);
 define("BCD_DEFAULT_MAR_Y2", 10);
 define("BCD_DEFAULT_TEXT_OFFSET", 2);
 define("BCD_I25_NARROW_BAR", 1);
 define("BCD_I25_WIDE_BAR", 2);
 define("BCS_I25_DRAW_CHECK", 2048);

 class I25Object extends BarcodeObject {
  var $mCharSet;
  function I25Object($Width, $Height, $Style, $Value) {
   $this->BarcodeObject($Width, $Height, $Style);
   $this->mValue=$Value;
   $this->mCharSet=array(
    /* 0 */ "00110",
    /* 1 */ "10001",
    /* 2 */ "01001",
    /* 3 */ "11000",
    /* 4 */ "00101",
    /* 5 */ "10100",
    /* 6 */ "01100",
    /* 7 */ "00011",
    /* 8 */ "10010",
    /* 9 */ "01010"
   );
  }
  function GetSize($xres) {
   $len = strlen($this->mValue);
   if ($len == 0) {
    $this->mError = "Null value";
    return false;
   };
   for ($i=0;$i<$len;$i++) {
    if ((ord($this->mValue[$i])<48) || (ord($this->mValue[$i])>57)) {
     $this->mError="I25 is numeric only";
     return false;
    };
  };
  if (($len%2) != 0) {
   $this->mError = "The length of barcode value must be even";
   return false;
  };
  $StartSize=BCD_I25_NARROW_BAR * 4 * $xres;
  $StopSize=BCD_I25_WIDE_BAR * $xres + 2 * BCD_I25_NARROW_BAR * $xres;
  $cPos=0;
  $sPos=0;
  do {
   $c1=$this->mValue[$cPos];
   $c2=$this->mValue[$cPos+1];
   $cset1= $this->mCharSet[$c1];
   $cset2=$this->mCharSet[$c2];
   for ($i=0;$i<5;$i++) {
    $type1 = ($cset1[$i]==0) ? (BCD_I25_NARROW_BAR * $xres) : (BCD_I25_WIDE_BAR * $xres);
    $type2 = ($cset2[$i]==0) ? (BCD_I25_NARROW_BAR * $xres) : (BCD_I25_WIDE_BAR * $xres);
    $sPos += ($type1 + $type2);
   };
   $cPos+=2;
  } while ($cPos<$len);
   return $sPos + $StartSize + $StopSize;
  }
  function DrawStart($DrawPos, $yPos, $ySize, $xres) {
   $this->DrawSingleBar($DrawPos, $yPos, BCD_I25_NARROW_BAR * $xres , $ySize);
   $DrawPos += BCD_I25_NARROW_BAR * $xres;
   $DrawPos += BCD_I25_NARROW_BAR * $xres;
   $this->DrawSingleBar($DrawPos, $yPos, BCD_I25_NARROW_BAR * $xres , $ySize);
   $DrawPos += BCD_I25_NARROW_BAR * $xres;
   $DrawPos += BCD_I25_NARROW_BAR * $xres;
   return $DrawPos;
  }
  function DrawStop($DrawPos, $yPos, $ySize, $xres) {
   $this->DrawSingleBar($DrawPos, $yPos, BCD_I25_WIDE_BAR * $xres , $ySize);
   $DrawPos += BCD_I25_WIDE_BAR  * $xres;
   $DrawPos += BCD_I25_NARROW_BAR  * $xres;
   $this->DrawSingleBar($DrawPos, $yPos, BCD_I25_NARROW_BAR  * $xres , $ySize);
   $DrawPos += BCD_I25_NARROW_BAR  * $xres; 
   return $DrawPos;
  }
  function DrawObject ($xres) {
   $len = strlen($this->mValue);
   if (($size = $this->GetSize($xres))==0) {
    return false;
   };
   $cPos = 0;
   if ($this->mStyle & BCS_DRAW_TEXT) {
    $ysize = $this->mHeight - BCD_DEFAULT_MAR_Y1 - BCD_DEFAULT_MAR_Y2 - $this->GetFontHeight($this->mFont);
   } else {
    $ysize = $this->mHeight - BCD_DEFAULT_MAR_Y1 - BCD_DEFAULT_MAR_Y2;
   };
   if ($this->mStyle & BCS_ALIGN_CENTER) {
    $sPos = (integer)(($this->mWidth - $size ) / 2);
   } elseif ($this->mStyle & BCS_ALIGN_RIGHT) {
    $sPos = $this->mWidth - $size;
   } else {
    $sPos = 0;
   };
   if ($this->mStyle & BCS_DRAW_TEXT) {
    if ($this->mStyle & BCS_STRETCH_TEXT) {
     for ($i=0;$i<$len;$i++) {
      $this->DrawChar($this->mFont, $sPos+BCD_I25_NARROW_BAR*4*$xres+($size/$len)*$i, $ysize + BCD_DEFAULT_MAR_Y1 + BCD_DEFAULT_TEXT_OFFSET , $this->mValue[$i]);
     };
    }else {
     $text_width = $this->GetFontWidth($this->mFont) * strlen($this->mValue);
     $this->DrawText($this->mFont, $sPos+(($size-$text_width)/2)+(BCD_I25_NARROW_BAR*4*$xres), $ysize + BCD_DEFAULT_MAR_Y1 + BCD_DEFAULT_TEXT_OFFSET, $this->mValue);
    };
   };
   $sPos = $this->DrawStart($sPos, BCD_DEFAULT_MAR_Y1, $ysize, $xres);
   do {
    $c1=$this->mValue[$cPos];
    $c2=$this->mValue[$cPos+1];
    $cset1=$this->mCharSet[$c1];
    $cset2=$this->mCharSet[$c2];
    for ($i=0;$i<5;$i++) {
     $type1 = ($cset1[$i]==0) ? (BCD_I25_NARROW_BAR * $xres) : (BCD_I25_WIDE_BAR * $xres);
     $type2 = ($cset2[$i]==0) ? (BCD_I25_NARROW_BAR * $xres) : (BCD_I25_WIDE_BAR * $xres);
     $this->DrawSingleBar($sPos, BCD_DEFAULT_MAR_Y1, $type1 , $ysize);
     $sPos += ($type1 + $type2);
    };
    $cPos+=2;
   } while ($cPos<$len);
   $sPos=$this->DrawStop($sPos, BCD_DEFAULT_MAR_Y1, $ysize, $xres);
   return true;
  }
 };

 class BarcodeObject {
  var $mWidth, $mHeight, $mStyle, $mBgcolor, $mBrush;
  var $mImg, $mFont;
  var $mError;
  function BarcodeObject ($Width = BCD_DEFAULT_Width, $Height = BCD_DEFAULT_HEIGHT, $Style = BCD_DEFAULT_STYLE) {
   $this->mWidth=$Width;
   $this->mHeight=$Height;
   $this->mStyle=$Style;
   $this->mFont=BCD_DEFAULT_FONT;
   $this->mImg=ImageCreate($this->mWidth, $this->mHeight);
   $dbColor=$this->mStyle & BCS_REVERSE_COLOR ? BCD_DEFAULT_FOREGROUND_COLOR : BCD_DEFAULT_BACKGROUND_COLOR;
   $dfColor=$this->mStyle & BCS_REVERSE_COLOR ? BCD_DEFAULT_BACKGROUND_COLOR : BCD_DEFAULT_FOREGROUND_COLOR;
   $this->mBgcolor=ImageColorAllocate($this->mImg, ($dbColor & 0xFF0000) >> 16, ($dbColor & 0x00FF00) >> 8, $dbColor & 0x0000FF);
   $this->mBrush=ImageColorAllocate($this->mImg, ($dfColor & 0xFF0000) >> 16, ($dfColor & 0x00FF00) >> 8, $dfColor & 0x0000FF);
   if (!($this->mStyle & BCS_TRANSPARENT)) {
    ImageFill($this->mImg, $this->mWidth, $this->mHeight, $this->mBgcolor);
   };
  }
  function DrawObject ($xres) {
   return false;
  }
  function DrawBorder () {
   ImageRectangle($this->mImg, 0, 0, $this->mWidth-1, $this->mHeight-1, $this->mBrush);
  }
  function DrawChar ($Font, $xPos, $yPos, $Char) {
   ImageString($this->mImg,$Font,$xPos,$yPos,$Char,$this->mBrush);
  }
  function DrawText ($Font, $xPos, $yPos, $Char) {
   ImageString($this->mImg,$Font,$xPos,$yPos,$Char,$this->mBrush);
  }
  function DrawSingleBar($xPos, $yPos, $xSize, $ySize) {
   if ($xPos>=0 && $xPos<=$this->mWidth  && ($xPos+$xSize)<=$this->mWidth && $yPos>=0 && $yPos<=$this->mHeight && ($yPos+$ySize)<=$this->mHeight) {
    for ($i=0;$i<$xSize;$i++) {
     ImageLine($this->mImg, $xPos+$i, $yPos, $xPos+$i, $yPos+$ySize, $this->mBrush);
    };
    return true;
   };
   return false;
  }
  function GetFontHeight($font) {
   return ImageFontHeight($font);
  }
  function GetFontWidth($font)  {
   return ImageFontWidth($font);
  }
  function SetFont($font) {
   $this->mFont = $font;
  }
  function GetStyle () {
   return $this->mStyle;
  }
  function SetStyle ($Style) {
   $this->mStyle = $Style;
  }
  function FlushObject () {
   if (($this->mStyle & BCS_BORDER)) {
    $this->DrawBorder();
   };
   Header("Content-Type: image/png");
   ImagePng($this->mImg);
  }
  function DestroyObject () {
   ImageDestroy($obj->mImg);
  }
 };

 $code=$_GET['code'];
 $style=((empty($_GET['style']))?BCD_DEFAULT_STYLE:$_GET['style']);
 $width=((empty($_GET['width']))?BCD_DEFAULT_WIDTH:$_GET['width']);
 $height=((empty($_GET['height']))?BCD_DEFAULT_HEIGHT:$_GET['height']);
 $xres=((empty($_GET['xres']))?BCD_DEFAULT_XRES:$_GET['xres']);
 $font=((empty($_GET['font']))?BCD_DEFAULT_FONT:$_GET['font']);
 $obj = new I25Object($width, $height, $style, $code);
 $obj->SetFont($font);
 $obj->DrawObject($xres);
 $obj->FlushObject();
 $obj->DestroyObject();
 unset($obj);
?>