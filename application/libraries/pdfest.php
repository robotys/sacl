<?php

	//require_once('/fpdi/fpdi_protection.php');
	
	//require_once("fpdf.php");
	//*require_once("fpdf_tpl.php");

	//*require_once("pdf_context.php");
	//*require_once("wrapper_functions.php");
	//*require_once("pdf_parser.php");
	//*require_once("fpdi_pdf_parser.php");

	///////////////////////////////////////////////////////
	////					FPDF START  		  	   ////
	///////////////////////////////////////////////////////
	
	/*******************************************************************************
	* Software: FPDF                                                               *
	* Version:  1.53                                                               *
	* Date:     2004-12-31                                                         *
	* Author:   Olivier PLATHEY                                                    *
	* License:  Freeware                                                           *
	*                                                                              *
	* You may use, modify and redistribute this software as you wish.              *
	*******************************************************************************/

	if(!class_exists('FPDF'))
	{
	define('FPDF_VERSION','1.53');

	class FPDF
	{
	//Private properties
	var $page;               //current page number
	var $n;                  //current object number
	var $offsets;            //array of object offsets
	var $buffer;             //buffer holding in-memory PDF
	var $pages;              //array containing pages
	var $state;              //current document state
	var $compress;           //compression flag
	var $DefOrientation;     //default orientation
	var $CurOrientation;     //current orientation
	var $OrientationChanges; //array indicating orientation changes
	var $k;                  //scale factor (number of points in user unit)
	var $fwPt,$fhPt;         //dimensions of page format in points
	var $fw,$fh;             //dimensions of page format in user unit
	var $wPt,$hPt;           //current dimensions of page in points
	var $w,$h;               //current dimensions of page in user unit
	var $lMargin;            //left margin
	var $tMargin;            //top margin
	var $rMargin;            //right margin
	var $bMargin;            //page break margin
	var $cMargin;            //cell margin
	var $x,$y;               //current position in user unit for cell positioning
	var $lasth;              //height of last cell printed
	var $LineWidth;          //line width in user unit
	var $CoreFonts;          //array of standard font names
	var $fonts;              //array of used fonts
	var $FontFiles;          //array of font files
	var $diffs;              //array of encoding differences
	var $images;             //array of used images
	var $PageLinks;          //array of links in pages
	var $links;              //array of internal links
	var $FontFamily;         //current font family
	var $FontStyle;          //current font style
	var $underline;          //underlining flag
	var $CurrentFont;        //current font info
	var $FontSizePt;         //current font size in points
	var $FontSize;           //current font size in user unit
	var $DrawColor;          //commands for drawing color
	var $FillColor;          //commands for filling color
	var $TextColor;          //commands for text color
	var $ColorFlag;          //indicates whether fill and text colors are different
	var $ws;                 //word spacing
	var $AutoPageBreak;      //automatic page breaking
	var $PageBreakTrigger;   //threshold used to trigger page breaks
	var $InFooter;           //flag set when processing footer
	var $ZoomMode;           //zoom display mode
	var $LayoutMode;         //layout display mode
	var $title;              //title
	var $subject;            //subject
	var $author;             //author
	var $keywords;           //keywords
	var $creator;            //creator
	var $AliasNbPages;       //alias for total number of pages
	var $PDFVersion;         //PDF version number

	/*******************************************************************************
	*                                                                              *
	*                               Public methods                                 *
	*                                                                              *
	*******************************************************************************/
	function FPDF($orientation='P',$unit='mm',$format='A4')
	{
		//Some checks
		$this->_dochecks();
		//Initialization of properties
		$this->page=0;
		$this->n=2;
		$this->buffer='';
		$this->pages=array();
		$this->OrientationChanges=array();
		$this->state=0;
		$this->fonts=array();
		$this->FontFiles=array();
		$this->diffs=array();
		$this->images=array();
		$this->links=array();
		$this->InFooter=false;
		$this->lasth=0;
		$this->FontFamily='';
		$this->FontStyle='';
		$this->FontSizePt=12;
		$this->underline=false;
		$this->DrawColor='0 G';
		$this->FillColor='0 g';
		$this->TextColor='0 g';
		$this->ColorFlag=false;
		$this->ws=0;
		//Standard fonts
		$this->CoreFonts=array('courier'=>'Courier','courierB'=>'Courier-Bold','courierI'=>'Courier-Oblique','courierBI'=>'Courier-BoldOblique',
			'helvetica'=>'Helvetica','helveticaB'=>'Helvetica-Bold','helveticaI'=>'Helvetica-Oblique','helveticaBI'=>'Helvetica-BoldOblique',
			'times'=>'Times-Roman','timesB'=>'Times-Bold','timesI'=>'Times-Italic','timesBI'=>'Times-BoldItalic',
			'symbol'=>'Symbol','zapfdingbats'=>'ZapfDingbats');
		//Scale factor
		if($unit=='pt')
			$this->k=1;
		elseif($unit=='mm')
			$this->k=72/25.4;
		elseif($unit=='cm')
			$this->k=72/2.54;
		elseif($unit=='in')
			$this->k=72;
		else
			$this->Error('Incorrect unit: '.$unit);
		//Page format
		if(is_string($format))
		{
			$format=strtolower($format);
			if($format=='a3')
				$format=array(841.89,1190.55);
			elseif($format=='a4')
				$format=array(595.28,841.89);
			elseif($format=='a5')
				$format=array(420.94,595.28);
			elseif($format=='letter')
				$format=array(612,792);
			elseif($format=='legal')
				$format=array(612,1008);
			else
				$this->Error('Unknown page format: '.$format);
			$this->fwPt=$format[0];
			$this->fhPt=$format[1];
		}
		else
		{
			$this->fwPt=$format[0]*$this->k;
			$this->fhPt=$format[1]*$this->k;
		}
		$this->fw=$this->fwPt/$this->k;
		$this->fh=$this->fhPt/$this->k;
		//Page orientation
		$orientation=strtolower($orientation);
		if($orientation=='p' || $orientation=='portrait')
		{
			$this->DefOrientation='P';
			$this->wPt=$this->fwPt;
			$this->hPt=$this->fhPt;
		}
		elseif($orientation=='l' || $orientation=='landscape')
		{
			$this->DefOrientation='L';
			$this->wPt=$this->fhPt;
			$this->hPt=$this->fwPt;
		}
		else
			$this->Error('Incorrect orientation: '.$orientation);
		$this->CurOrientation=$this->DefOrientation;
		$this->w=$this->wPt/$this->k;
		$this->h=$this->hPt/$this->k;
		//Page margins (1 cm)
		$margin=28.35/$this->k;
		$this->SetMargins($margin,$margin);
		//Interior cell margin (1 mm)
		$this->cMargin=$margin/10;
		//Line width (0.2 mm)
		$this->LineWidth=.567/$this->k;
		//Automatic page break
		$this->SetAutoPageBreak(true,2*$margin);
		//Full width display mode
		$this->SetDisplayMode('fullwidth');
		//Enable compression
		$this->SetCompression(true);
		//Set default PDF version number
		$this->PDFVersion='1.3';
	}

	function SetMargins($left,$top,$right=-1)
	{
		//Set left, top and right margins
		$this->lMargin=$left;
		$this->tMargin=$top;
		if($right==-1)
			$right=$left;
		$this->rMargin=$right;
	}

	function SetLeftMargin($margin)
	{
		//Set left margin
		$this->lMargin=$margin;
		if($this->page>0 && $this->x<$margin)
			$this->x=$margin;
	}

	function SetTopMargin($margin)
	{
		//Set top margin
		$this->tMargin=$margin;
	}

	function SetRightMargin($margin)
	{
		//Set right margin
		$this->rMargin=$margin;
	}

	function SetAutoPageBreak($auto,$margin=0)
	{
		//Set auto page break mode and triggering margin
		$this->AutoPageBreak=$auto;
		$this->bMargin=$margin;
		$this->PageBreakTrigger=$this->h-$margin;
	}

	function SetDisplayMode($zoom,$layout='continuous')
	{
		//Set display mode in viewer
		if($zoom=='fullpage' || $zoom=='fullwidth' || $zoom=='real' || $zoom=='default' || !is_string($zoom))
			$this->ZoomMode=$zoom;
		else
			$this->Error('Incorrect zoom display mode: '.$zoom);
		if($layout=='single' || $layout=='continuous' || $layout=='two' || $layout=='default')
			$this->LayoutMode=$layout;
		else
			$this->Error('Incorrect layout display mode: '.$layout);
	}

	function SetCompression($compress)
	{
		//Set page compression
		if(function_exists('gzcompress'))
			$this->compress=$compress;
		else
			$this->compress=false;
	}

	function SetTitle($title)
	{
		//Title of document
		$this->title=$title;
	}

	function SetSubject($subject)
	{
		//Subject of document
		$this->subject=$subject;
	}

	function SetAuthor($author)
	{
		//Author of document
		$this->author=$author;
	}

	function SetKeywords($keywords)
	{
		//Keywords of document
		$this->keywords=$keywords;
	}

	function SetCreator($creator)
	{
		//Creator of document
		$this->creator=$creator;
	}

	function AliasNbPages($alias='{nb}')
	{
		//Define an alias for total number of pages
		$this->AliasNbPages=$alias;
	}

	function Error($msg)
	{
		//Fatal error
		die('<B>FPDF error: </B>'.$msg);
	}

	function Open()
	{
		//Begin document
		$this->state=1;
	}

	function Close()
	{
		//Terminate document
		if($this->state==3)
			return;
		if($this->page==0)
			$this->AddPage();
		//Page footer
		$this->InFooter=true;
		$this->Footer();
		$this->InFooter=false;
		//Close page
		$this->_endpage();
		//Close document
		$this->_enddoc();
	}

	function AddPage($orientation='')
	{
		//Start a new page
		if($this->state==0)
			$this->Open();
		$family=$this->FontFamily;
		$style=$this->FontStyle.($this->underline ? 'U' : '');
		$size=$this->FontSizePt;
		$lw=$this->LineWidth;
		$dc=$this->DrawColor;
		$fc=$this->FillColor;
		$tc=$this->TextColor;
		$cf=$this->ColorFlag;
		if($this->page>0)
		{
			//Page footer
			$this->InFooter=true;
			$this->Footer();
			$this->InFooter=false;
			//Close page
			$this->_endpage();
		}
		//Start new page
		$this->_beginpage($orientation);
		//Set line cap style to square
		$this->_out('2 J');
		//Set line width
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.2f w',$lw*$this->k));
		//Set font
		if($family)
			$this->SetFont($family,$style,$size);
		//Set colors
		$this->DrawColor=$dc;
		if($dc!='0 G')
			$this->_out($dc);
		$this->FillColor=$fc;
		if($fc!='0 g')
			$this->_out($fc);
		$this->TextColor=$tc;
		$this->ColorFlag=$cf;
		//Page header
		$this->Header();
		//Restore line width
		if($this->LineWidth!=$lw)
		{
			$this->LineWidth=$lw;
			$this->_out(sprintf('%.2f w',$lw*$this->k));
		}
		//Restore font
		if($family)
			$this->SetFont($family,$style,$size);
		//Restore colors
		if($this->DrawColor!=$dc)
		{
			$this->DrawColor=$dc;
			$this->_out($dc);
		}
		if($this->FillColor!=$fc)
		{
			$this->FillColor=$fc;
			$this->_out($fc);
		}
		$this->TextColor=$tc;
		$this->ColorFlag=$cf;
	}

	function Header()
	{
		//To be implemented in your own inherited class
	}

	function Footer()
	{
		//To be implemented in your own inherited class
	}

	function PageNo()
	{
		//Get current page number
		return $this->page;
	}

	function SetDrawColor($r,$g=-1,$b=-1)
	{
		//Set color for all stroking operations
		if(($r==0 && $g==0 && $b==0) || $g==-1)
			$this->DrawColor=sprintf('%.3f G',$r/255);
		else
			$this->DrawColor=sprintf('%.3f %.3f %.3f RG',$r/255,$g/255,$b/255);
		if($this->page>0)
			$this->_out($this->DrawColor);
	}

	function SetFillColor($r,$g=-1,$b=-1)
	{
		//Set color for all filling operations
		if(($r==0 && $g==0 && $b==0) || $g==-1)
			$this->FillColor=sprintf('%.3f g',$r/255);
		else
			$this->FillColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
		$this->ColorFlag=($this->FillColor!=$this->TextColor);
		if($this->page>0)
			$this->_out($this->FillColor);
	}

	function SetTextColor($r,$g=-1,$b=-1)
	{
		//Set color for text
		if(($r==0 && $g==0 && $b==0) || $g==-1)
			$this->TextColor=sprintf('%.3f g',$r/255);
		else
			$this->TextColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
		$this->ColorFlag=($this->FillColor!=$this->TextColor);
	}

	function GetStringWidth($s)
	{
		//Get width of a string in the current font
		$s=(string)$s;
		$cw=&$this->CurrentFont['cw'];
		$w=0;
		$l=strlen($s);
		for($i=0;$i<$l;$i++)
			$w+=$cw[$s{$i}];
		return $w*$this->FontSize/1000;
	}

	function SetLineWidth($width)
	{
		//Set line width
		$this->LineWidth=$width;
		if($this->page>0)
			$this->_out(sprintf('%.2f w',$width*$this->k));
	}

	function Line($x1,$y1,$x2,$y2)
	{
		//Draw a line
		$this->_out(sprintf('%.2f %.2f m %.2f %.2f l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
	}

	function Rect($x,$y,$w,$h,$style='')
	{
		//Draw a rectangle
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
			$op='B';
		else
			$op='S';
		$this->_out(sprintf('%.2f %.2f %.2f %.2f re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
	}

	function AddFont($family,$style='',$file='')
	{
		//Add a TrueType or Type1 font
		$family=strtolower($family);
		if($file=='')
			$file=str_replace(' ','',$family).strtolower($style).'.php';
		if($family=='arial')
			$family='helvetica';
		$style=strtoupper($style);
		if($style=='IB')
			$style='BI';
		$fontkey=$family.$style;
		if(isset($this->fonts[$fontkey]))
			$this->Error('Font already added: '.$family.' '.$style);
		include($this->_getfontpath().$file);
		if(!isset($name))
			$this->Error('Could not include font definition file');
		$i=count($this->fonts)+1;
		$this->fonts[$fontkey]=array('i'=>$i,'type'=>$type,'name'=>$name,'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw,'enc'=>$enc,'file'=>$file);
		if($diff)
		{
			//Search existing encodings
			$d=0;
			$nb=count($this->diffs);
			for($i=1;$i<=$nb;$i++)
			{
				if($this->diffs[$i]==$diff)
				{
					$d=$i;
					break;
				}
			}
			if($d==0)
			{
				$d=$nb+1;
				$this->diffs[$d]=$diff;
			}
			$this->fonts[$fontkey]['diff']=$d;
		}
		if($file)
		{
			if($type=='TrueType')
				$this->FontFiles[$file]=array('length1'=>$originalsize);
			else
				$this->FontFiles[$file]=array('length1'=>$size1,'length2'=>$size2);
		}
	}

	function SetFont($family,$style='',$size=0)
	{
		//Select a font; size given in points
		global $fpdf_charwidths;

		$family=strtolower($family);
		if($family=='')
			$family=$this->FontFamily;
		if($family=='arial')
			$family='helvetica';
		elseif($family=='symbol' || $family=='zapfdingbats')
			$style='';
		$style=strtoupper($style);
		if(strpos($style,'U')!==false)
		{
			$this->underline=true;
			$style=str_replace('U','',$style);
		}
		else
			$this->underline=false;
		if($style=='IB')
			$style='BI';
		if($size==0)
			$size=$this->FontSizePt;
		//Test if font is already selected
		if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
			return;
		//Test if used for the first time
		$fontkey=$family.$style;
		if(!isset($this->fonts[$fontkey]))
		{
			//Check if one of the standard fonts
			if(isset($this->CoreFonts[$fontkey]))
			{
				if(!isset($fpdf_charwidths[$fontkey]))
				{
					//Load metric file
					$file=$family;
					if($family=='times' || $family=='helvetica')
						$file.=strtolower($style);
					//include($this->_getfontpath().$file.'.php');
					$type = 'Core';
					$name = 'Helvetica-Oblique';
					$up = -100;
					$ut = 50;
					$cw = array(
						chr(0)=>278,chr(1)=>278,chr(2)=>278,chr(3)=>278,chr(4)=>278,chr(5)=>278,chr(6)=>278,chr(7)=>278,chr(8)=>278,chr(9)=>278,chr(10)=>278,chr(11)=>278,chr(12)=>278,chr(13)=>278,chr(14)=>278,chr(15)=>278,chr(16)=>278,chr(17)=>278,chr(18)=>278,chr(19)=>278,chr(20)=>278,chr(21)=>278,
						chr(22)=>278,chr(23)=>278,chr(24)=>278,chr(25)=>278,chr(26)=>278,chr(27)=>278,chr(28)=>278,chr(29)=>278,chr(30)=>278,chr(31)=>278,' '=>278,'!'=>278,'"'=>355,'#'=>556,'$'=>556,'%'=>889,'&'=>667,'\''=>191,'('=>333,')'=>333,'*'=>389,'+'=>584,
						','=>278,'-'=>333,'.'=>278,'/'=>278,'0'=>556,'1'=>556,'2'=>556,'3'=>556,'4'=>556,'5'=>556,'6'=>556,'7'=>556,'8'=>556,'9'=>556,':'=>278,';'=>278,'<'=>584,'='=>584,'>'=>584,'?'=>556,'@'=>1015,'A'=>667,
						'B'=>667,'C'=>722,'D'=>722,'E'=>667,'F'=>611,'G'=>778,'H'=>722,'I'=>278,'J'=>500,'K'=>667,'L'=>556,'M'=>833,'N'=>722,'O'=>778,'P'=>667,'Q'=>778,'R'=>722,'S'=>667,'T'=>611,'U'=>722,'V'=>667,'W'=>944,
						'X'=>667,'Y'=>667,'Z'=>611,'['=>278,'\\'=>278,']'=>278,'^'=>469,'_'=>556,'`'=>333,'a'=>556,'b'=>556,'c'=>500,'d'=>556,'e'=>556,'f'=>278,'g'=>556,'h'=>556,'i'=>222,'j'=>222,'k'=>500,'l'=>222,'m'=>833,
						'n'=>556,'o'=>556,'p'=>556,'q'=>556,'r'=>333,'s'=>500,'t'=>278,'u'=>556,'v'=>500,'w'=>722,'x'=>500,'y'=>500,'z'=>500,'{'=>334,'|'=>260,'}'=>334,'~'=>584,chr(127)=>350,chr(128)=>556,chr(129)=>350,chr(130)=>222,chr(131)=>556,
						chr(132)=>333,chr(133)=>1000,chr(134)=>556,chr(135)=>556,chr(136)=>333,chr(137)=>1000,chr(138)=>667,chr(139)=>333,chr(140)=>1000,chr(141)=>350,chr(142)=>611,chr(143)=>350,chr(144)=>350,chr(145)=>222,chr(146)=>222,chr(147)=>333,chr(148)=>333,chr(149)=>350,chr(150)=>556,chr(151)=>1000,chr(152)=>333,chr(153)=>1000,
						chr(154)=>500,chr(155)=>333,chr(156)=>944,chr(157)=>350,chr(158)=>500,chr(159)=>667,chr(160)=>278,chr(161)=>333,chr(162)=>556,chr(163)=>556,chr(164)=>556,chr(165)=>556,chr(166)=>260,chr(167)=>556,chr(168)=>333,chr(169)=>737,chr(170)=>370,chr(171)=>556,chr(172)=>584,chr(173)=>333,chr(174)=>737,chr(175)=>333,
						chr(176)=>400,chr(177)=>584,chr(178)=>333,chr(179)=>333,chr(180)=>333,chr(181)=>556,chr(182)=>537,chr(183)=>278,chr(184)=>333,chr(185)=>333,chr(186)=>365,chr(187)=>556,chr(188)=>834,chr(189)=>834,chr(190)=>834,chr(191)=>611,chr(192)=>667,chr(193)=>667,chr(194)=>667,chr(195)=>667,chr(196)=>667,chr(197)=>667,
						chr(198)=>1000,chr(199)=>722,chr(200)=>667,chr(201)=>667,chr(202)=>667,chr(203)=>667,chr(204)=>278,chr(205)=>278,chr(206)=>278,chr(207)=>278,chr(208)=>722,chr(209)=>722,chr(210)=>778,chr(211)=>778,chr(212)=>778,chr(213)=>778,chr(214)=>778,chr(215)=>584,chr(216)=>778,chr(217)=>722,chr(218)=>722,chr(219)=>722,
						chr(220)=>722,chr(221)=>667,chr(222)=>667,chr(223)=>611,chr(224)=>556,chr(225)=>556,chr(226)=>556,chr(227)=>556,chr(228)=>556,chr(229)=>556,chr(230)=>889,chr(231)=>500,chr(232)=>556,chr(233)=>556,chr(234)=>556,chr(235)=>556,chr(236)=>278,chr(237)=>278,chr(238)=>278,chr(239)=>278,chr(240)=>556,chr(241)=>556,
						chr(242)=>556,chr(243)=>556,chr(244)=>556,chr(245)=>556,chr(246)=>556,chr(247)=>584,chr(248)=>611,chr(249)=>556,chr(250)=>556,chr(251)=>556,chr(252)=>556,chr(253)=>500,chr(254)=>556,chr(255)=>500);
					$fpdf_charwidths[$fontkey] = $cw;
					if(!isset($fpdf_charwidths[$fontkey])) 
						$this->Error('Could not include font metric file');
				}
				$i=count($this->fonts)+1;
				$this->fonts[$fontkey]=array('i'=>$i,'type'=>'core','name'=>$this->CoreFonts[$fontkey],'up'=>-100,'ut'=>50,'cw'=>$fpdf_charwidths[$fontkey]);
			}
			else
				$this->Error('Undefined font: '.$family.' '.$style);
		}
		//Select it
		$this->FontFamily=$family;
		$this->FontStyle=$style;
		$this->FontSizePt=$size;
		$this->FontSize=$size/$this->k;
		$this->CurrentFont=&$this->fonts[$fontkey];
		if($this->page>0)
			$this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
	}

	function SetFontSize($size)
	{
		//Set font size in points
		if($this->FontSizePt==$size)
			return;
		$this->FontSizePt=$size;
		$this->FontSize=$size/$this->k;
		if($this->page>0)
			$this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
	}

	function AddLink()
	{
		//Create a new internal link
		$n=count($this->links)+1;
		$this->links[$n]=array(0,0);
		return $n;
	}

	function SetLink($link,$y=0,$page=-1)
	{
		//Set destination of internal link
		if($y==-1)
			$y=$this->y;
		if($page==-1)
			$page=$this->page;
		$this->links[$link]=array($page,$y);
	}

	function Link($x,$y,$w,$h,$link)
	{
		//Put a link on the page
		$this->PageLinks[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
	}

	function Text($x,$y,$txt)
	{
		//Output a string
		$s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
		if($this->underline && $txt!='')
			$s.=' '.$this->_dounderline($x,$y,$txt);
		if($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	}

	function AcceptPageBreak()
	{
		//Accept automatic page break or not
		return $this->AutoPageBreak;
	}

	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
	{
		//Output a cell
		$k=$this->k;
		if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
		{
			//Automatic page break
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			$this->x=$x;
			if($ws>0)
			{
				$this->ws=$ws;
				$this->_out(sprintf('%.3f Tw',$ws*$k));
			}
		}
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$s='';
		if($fill==1 || $border==1)
		{
			if($fill==1)
				$op=($border==1) ? 'B' : 'f';
			else
				$op='S';
			$s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		}
		if(is_string($border))
		{
			$x=$this->x;
			$y=$this->y;
			if(strpos($border,'L')!==false)
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			if(strpos($border,'T')!==false)
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
			if(strpos($border,'R')!==false)
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			if(strpos($border,'B')!==false)
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		}
		if($txt!=='')
		{
			if($align=='R')
				$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
			elseif($align=='C')
				$dx=($w-$this->GetStringWidth($txt))/2;
			else
				$dx=$this->cMargin;
			if($this->ColorFlag)
				$s.='q '.$this->TextColor.' ';
			$txt2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
			$s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
			if($this->underline)
				$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			if($this->ColorFlag)
				$s.=' Q';
			if($link)
				$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
		}
		if($s)
			$this->_out($s);
		$this->lasth=$h;
		if($ln>0)
		{
			//Go to next line
			$this->y+=$h;
			if($ln==1)
				$this->x=$this->lMargin;
		}
		else
			$this->x+=$w;
	}

	function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0)
	{
		//Output text with automatic or explicit line breaks
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 && $s[$nb-1]=="\n")
			$nb--;
		$b=0;
		if($border)
		{
			if($border==1)
			{
				$border='LTRB';
				$b='LRT';
				$b2='LR';
			}
			else
			{
				$b2='';
				if(strpos($border,'L')!==false)
					$b2.='L';
				if(strpos($border,'R')!==false)
					$b2.='R';
				$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
			}
		}
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$ns=0;
		$nl=1;
		while($i<$nb)
		{
			//Get next character
			$c=$s{$i};
			if($c=="\n")
			{
				//Explicit line break
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$ns=0;
				$nl++;
				if($border && $nl==2)
					$b=$b2;
				continue;
			}
			if($c==' ')
			{
				$sep=$i;
				$ls=$l;
				$ns++;
			}
			$l+=$cw[$c];
			if($l>$wmax)
			{
				//Automatic line break
				if($sep==-1)
				{
					if($i==$j)
						$i++;
					if($this->ws>0)
					{
						$this->ws=0;
						$this->_out('0 Tw');
					}
					$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
				}
				else
				{
					if($align=='J')
					{
						$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
						$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
					}
					$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
					$i=$sep+1;
				}
				$sep=-1;
				$j=$i;
				$l=0;
				$ns=0;
				$nl++;
				if($border && $nl==2)
					$b=$b2;
			}
			else
				$i++;
		}
		//Last chunk
		if($this->ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		if($border && strpos($border,'B')!==false)
			$b.='B';
		$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
		$this->x=$this->lMargin;
	}

	function Write($h,$txt,$link='')
	{
		//Output text in flowing mode
		$cw=&$this->CurrentFont['cw'];
		$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			//Get next character
			$c=$s{$i};
			if($c=="\n")
			{
				//Explicit line break
				$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				if($nl==1)
				{
					$this->x=$this->lMargin;
					$w=$this->w-$this->rMargin-$this->x;
					$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				}
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				//Automatic line break
				if($sep==-1)
				{
					if($this->x>$this->lMargin)
					{
						//Move to next line
						$this->x=$this->lMargin;
						$this->y+=$h;
						$w=$this->w-$this->rMargin-$this->x;
						$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
						$i++;
						$nl++;
						continue;
					}
					if($i==$j)
						$i++;
					$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
				}
				else
				{
					$this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
					$i=$sep+1;
				}
				$sep=-1;
				$j=$i;
				$l=0;
				if($nl==1)
				{
					$this->x=$this->lMargin;
					$w=$this->w-$this->rMargin-$this->x;
					$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				}
				$nl++;
			}
			else
				$i++;
		}
		//Last chunk
		if($i!=$j)
			$this->Cell($l/1000*$this->FontSize,$h,substr($s,$j),0,0,'',0,$link);
	}

	function Image($file,$x,$y,$w=0,$h=0,$type='',$link='')
	{
		//Put an image on the page
		if(!isset($this->images[$file]))
		{
			//First use of image, get info
			if($type=='')
			{
				$pos=strrpos($file,'.');
				if(!$pos)
					$this->Error('Image file has no extension and no type was specified: '.$file);
				$type=substr($file,$pos+1);
			}
			$type=strtolower($type);
			$mqr=get_magic_quotes_runtime();
			//set_magic_quotes_runtime(0);
			if($type=='jpg' || $type=='jpeg')
				$info=$this->_parsejpg($file);
			elseif($type=='png')
				$info=$this->_parsepng($file);
			else
			{
				//Allow for additional formats
				$mtd='_parse'.$type;
				if(!method_exists($this,$mtd))
					$this->Error('Unsupported image type: '.$type);
				$info=$this->$mtd($file);
			}
			//set_magic_quotes_runtime($mqr);
			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
		else
			$info=$this->images[$file];
		//Automatic width and height calculation if needed
		if($w==0 && $h==0)
		{
			//Put image at 72 dpi
			$w=$info['w']/$this->k;
			$h=$info['h']/$this->k;
		}
		if($w==0)
			$w=$h*$info['w']/$info['h'];
		if($h==0)
			$h=$w*$info['h']/$info['w'];
		$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
		if($link)
			$this->Link($x,$y,$w,$h,$link);
	}

	function Ln($h='')
	{
		//Line feed; default value is last cell height
		$this->x=$this->lMargin;
		if(is_string($h))
			$this->y+=$this->lasth;
		else
			$this->y+=$h;
	}

	function GetX()
	{
		//Get x position
		return $this->x;
	}

	function SetX($x)
	{
		//Set x position
		if($x>=0)
			$this->x=$x;
		else
			$this->x=$this->w+$x;
	}

	function GetY()
	{
		//Get y position
		return $this->y;
	}

	function SetY($y)
	{
		//Set y position and reset x
		$this->x=$this->lMargin;
		if($y>=0)
			$this->y=$y;
		else
			$this->y=$this->h+$y;
	}

	function SetXY($x,$y)
	{
		//Set x and y positions
		$this->SetY($y);
		$this->SetX($x);
	}

	function Output($name='',$dest='')
	{
		//Output PDF to some destination
		//Finish document if necessary
		if($this->state<3)
			$this->Close();
		//Normalize parameters
		if(is_bool($dest))
			$dest=$dest ? 'D' : 'F';
		$dest=strtoupper($dest);
		if($dest=='')
		{
			if($name=='')
			{
				$name='doc.pdf';
				$dest='I';
			}
			else
				$dest='F';
		}
		switch($dest)
		{
			case 'I':
				//Send to standard output
				if(ob_get_contents())
					$this->Error('Some data has already been output, can\'t send PDF file');
				if(php_sapi_name()!='cli')
				{
					//We send to a browser
					header('Content-Type: application/pdf');
					if(headers_sent())
						$this->Error('Some data has already been output to browser, can\'t send PDF file');
					header('Content-Length: '.strlen($this->buffer));
					header('Content-disposition: inline; filename="'.$name.'"');
				}
				echo $this->buffer;
				break;
			case 'D':
				//Download file
				if(ob_get_contents())
					$this->Error('Some data has already been output, can\'t send PDF file');
				if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
					header('Content-Type: application/force-download');
				else
					header('Content-Type: application/octet-stream');
				if(headers_sent())
					$this->Error('Some data has already been output to browser, can\'t send PDF file');
				header('Content-Length: '.strlen($this->buffer));
				header('Content-disposition: attachment; filename="'.$name.'"');
				echo $this->buffer;
				break;
			case 'F':
				//Save to local file
				$f=fopen($name,'wb');
				if(!$f)
					$this->Error('Unable to create output file: '.$name);
				fwrite($f,$this->buffer,strlen($this->buffer));
				fclose($f);
				break;
			case 'S':
				//Return as a string
				return $this->buffer;
			default:
				$this->Error('Incorrect output destination: '.$dest);
		}
		return '';
	}

	/*******************************************************************************
	*                                                                              *
	*                              Protected methods                               *
	*                                                                              *
	*******************************************************************************/
	function _dochecks()
	{
		//Check for locale-related bug
		if(1.1==1)
			$this->Error('Don\'t alter the locale before including class file');
		//Check for decimal separator
		if(sprintf('%.1f',1.0)!='1.0')
			setlocale(LC_NUMERIC,'C');
	}

	function _getfontpath()
	{
		if(!defined('FPDF_FONTPATH') && is_dir(dirname(__FILE__).'/font'))
			define('FPDF_FONTPATH',dirname(__FILE__).'/font/');
		return defined('FPDF_FONTPATH') ? FPDF_FONTPATH : '';
	}

	function _putpages()
	{
		$nb=$this->page;
		if(!empty($this->AliasNbPages))
		{
			//Replace number of pages
			for($n=1;$n<=$nb;$n++)
				$this->pages[$n]=str_replace($this->AliasNbPages,$nb,$this->pages[$n]);
		}
		if($this->DefOrientation=='P')
		{
			$wPt=$this->fwPt;
			$hPt=$this->fhPt;
		}
		else
		{
			$wPt=$this->fhPt;
			$hPt=$this->fwPt;
		}
		$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		for($n=1;$n<=$nb;$n++)
		{
			//Page
			$this->_newobj();
			$this->_out('<</Type /Page');
			$this->_out('/Parent 1 0 R');
			if(isset($this->OrientationChanges[$n]))
				$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$hPt,$wPt));
			$this->_out('/Resources 2 0 R');
			if(isset($this->PageLinks[$n]))
			{
				//Links
				$annots='/Annots [';
				foreach($this->PageLinks[$n] as $pl)
				{
					$rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
					$annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
					if(is_string($pl[4]))
						$annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
					else
					{
						$l=$this->links[$pl[4]];
						$h=isset($this->OrientationChanges[$l[0]]) ? $wPt : $hPt;
						$annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$h-$l[1]*$this->k);
					}
				}
				$this->_out($annots.']');
			}
			$this->_out('/Contents '.($this->n+1).' 0 R>>');
			$this->_out('endobj');
			//Page content
			$p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
			$this->_newobj();
			$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
			$this->_putstream($p);
			$this->_out('endobj');
		}
		//Pages root
		$this->offsets[1]=strlen($this->buffer);
		$this->_out('1 0 obj');
		$this->_out('<</Type /Pages');
		$kids='/Kids [';
		for($i=0;$i<$nb;$i++)
			$kids.=(3+2*$i).' 0 R ';
		$this->_out($kids.']');
		$this->_out('/Count '.$nb);
		$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putfonts()
	{
		$nf=$this->n;
		foreach($this->diffs as $diff)
		{
			//Encodings
			$this->_newobj();
			$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
			$this->_out('endobj');
		}
		$mqr=get_magic_quotes_runtime();
		//set_magic_quotes_runtime(0);
		foreach($this->FontFiles as $file=>$info)
		{
			//Font file embedding
			$this->_newobj();
			$this->FontFiles[$file]['n']=$this->n;
			$font='';
			$f=fopen($this->_getfontpath().$file,'rb',1);
			if(!$f)
				$this->Error('Font file not found');
			while(!feof($f))
				$font.=fread($f,8192);
			fclose($f);
			$compressed=(substr($file,-2)=='.z');
			if(!$compressed && isset($info['length2']))
			{
				$header=(ord($font{0})==128);
				if($header)
				{
					//Strip first binary header
					$font=substr($font,6);
				}
				if($header && ord($font{$info['length1']})==128)
				{
					//Strip second binary header
					$font=substr($font,0,$info['length1']).substr($font,$info['length1']+6);
				}
			}
			$this->_out('<</Length '.strlen($font));
			if($compressed)
				$this->_out('/Filter /FlateDecode');
			$this->_out('/Length1 '.$info['length1']);
			if(isset($info['length2']))
				$this->_out('/Length2 '.$info['length2'].' /Length3 0');
			$this->_out('>>');
			$this->_putstream($font);
			$this->_out('endobj');
		}
		//set_magic_quotes_runtime($mqr);
		foreach($this->fonts as $k=>$font)
		{
			//Font objects
			$this->fonts[$k]['n']=$this->n+1;
			$type=$font['type'];
			$name=$font['name'];
			if($type=='core')
			{
				//Standard font
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/BaseFont /'.$name);
				$this->_out('/Subtype /Type1');
				if($name!='Symbol' && $name!='ZapfDingbats')
					$this->_out('/Encoding /WinAnsiEncoding');
				$this->_out('>>');
				$this->_out('endobj');
			}
			elseif($type=='Type1' || $type=='TrueType')
			{
				//Additional Type1 or TrueType font
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/BaseFont /'.$name);
				$this->_out('/Subtype /'.$type);
				$this->_out('/FirstChar 32 /LastChar 255');
				$this->_out('/Widths '.($this->n+1).' 0 R');
				$this->_out('/FontDescriptor '.($this->n+2).' 0 R');
				if($font['enc'])
				{
					if(isset($font['diff']))
						$this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
					else
						$this->_out('/Encoding /WinAnsiEncoding');
				}
				$this->_out('>>');
				$this->_out('endobj');
				//Widths
				$this->_newobj();
				$cw=&$font['cw'];
				$s='[';
				for($i=32;$i<=255;$i++)
					$s.=$cw[chr($i)].' ';
				$this->_out($s.']');
				$this->_out('endobj');
				//Descriptor
				$this->_newobj();
				$s='<</Type /FontDescriptor /FontName /'.$name;
				foreach($font['desc'] as $k=>$v)
					$s.=' /'.$k.' '.$v;
				$file=$font['file'];
				if($file)
					$s.=' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
				$this->_out($s.'>>');
				$this->_out('endobj');
			}
			else
			{
				//Allow for additional types
				$mtd='_put'.strtolower($type);
				if(!method_exists($this,$mtd))
					$this->Error('Unsupported font type: '.$type);
				$this->$mtd($font);
			}
		}
	}

	function _putimages()
	{
		$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		reset($this->images);
		while(list($file,$info)=each($this->images))
		{
			$this->_newobj();
			$this->images[$file]['n']=$this->n;
			$this->_out('<</Type /XObject');
			$this->_out('/Subtype /Image');
			$this->_out('/Width '.$info['w']);
			$this->_out('/Height '.$info['h']);
			if($info['cs']=='Indexed')
				$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
			else
			{
				$this->_out('/ColorSpace /'.$info['cs']);
				if($info['cs']=='DeviceCMYK')
					$this->_out('/Decode [1 0 1 0 1 0 1 0]');
			}
			$this->_out('/BitsPerComponent '.$info['bpc']);
			if(isset($info['f']))
				$this->_out('/Filter /'.$info['f']);
			if(isset($info['parms']))
				$this->_out($info['parms']);
			if(isset($info['trns']) && is_array($info['trns']))
			{
				$trns='';
				for($i=0;$i<count($info['trns']);$i++)
					$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
				$this->_out('/Mask ['.$trns.']');
			}
			$this->_out('/Length '.strlen($info['data']).'>>');
			$this->_putstream($info['data']);
			unset($this->images[$file]['data']);
			$this->_out('endobj');
			//Palette
			if($info['cs']=='Indexed')
			{
				$this->_newobj();
				$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
				$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
				$this->_putstream($pal);
				$this->_out('endobj');
			}
		}
	}

	function _putxobjectdict()
	{
		foreach($this->images as $image)
			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
	}

	function _putresourcedict()
	{
		$this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
		$this->_out('/Font <<');
		foreach($this->fonts as $font)
			$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
		$this->_out('>>');
		$this->_out('/XObject <<');
		$this->_putxobjectdict();
		$this->_out('>>');
	}

	function _putresources()
	{
		$this->_putfonts();
		$this->_putimages();
		//Resource dictionary
		$this->offsets[2]=strlen($this->buffer);
		$this->_out('2 0 obj');
		$this->_out('<<');
		$this->_putresourcedict();
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putinfo()
	{
		$this->_out('/Producer '.$this->_textstring('FPDF '.FPDF_VERSION));
		if(!empty($this->title))
			$this->_out('/Title '.$this->_textstring($this->title));
		if(!empty($this->subject))
			$this->_out('/Subject '.$this->_textstring($this->subject));
		if(!empty($this->author))
			$this->_out('/Author '.$this->_textstring($this->author));
		if(!empty($this->keywords))
			$this->_out('/Keywords '.$this->_textstring($this->keywords));
		if(!empty($this->creator))
			$this->_out('/Creator '.$this->_textstring($this->creator));
		$this->_out('/CreationDate '.$this->_textstring('D:'.date('YmdHis')));
	}

	function _putcatalog()
	{
		$this->_out('/Type /Catalog');
		$this->_out('/Pages 1 0 R');
		if($this->ZoomMode=='fullpage')
			$this->_out('/OpenAction [3 0 R /Fit]');
		elseif($this->ZoomMode=='fullwidth')
			$this->_out('/OpenAction [3 0 R /FitH null]');
		elseif($this->ZoomMode=='real')
			$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
		elseif(!is_string($this->ZoomMode))
			$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
		if($this->LayoutMode=='single')
			$this->_out('/PageLayout /SinglePage');
		elseif($this->LayoutMode=='continuous')
			$this->_out('/PageLayout /OneColumn');
		elseif($this->LayoutMode=='two')
			$this->_out('/PageLayout /TwoColumnLeft');
	}

	function _putheader()
	{
		$this->_out('%PDF-'.$this->PDFVersion);
	}

	function _puttrailer()
	{
		$this->_out('/Size '.($this->n+1));
		$this->_out('/Root '.$this->n.' 0 R');
		$this->_out('/Info '.($this->n-1).' 0 R');
	}

	function _enddoc()
	{
		$this->_putheader();
		$this->_putpages();
		$this->_putresources();
		//Info
		$this->_newobj();
		$this->_out('<<');
		$this->_putinfo();
		$this->_out('>>');
		$this->_out('endobj');
		//Catalog
		$this->_newobj();
		$this->_out('<<');
		$this->_putcatalog();
		$this->_out('>>');
		$this->_out('endobj');
		//Cross-ref
		$o=strlen($this->buffer);
		$this->_out('xref');
		$this->_out('0 '.($this->n+1));
		$this->_out('0000000000 65535 f ');
		for($i=1;$i<=$this->n;$i++)
			$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
		//Trailer
		$this->_out('trailer');
		$this->_out('<<');
		$this->_puttrailer();
		$this->_out('>>');
		$this->_out('startxref');
		$this->_out($o);
		$this->_out('%%EOF');
		$this->state=3;
	}

	function _beginpage($orientation)
	{
		$this->page++;
		$this->pages[$this->page]='';
		$this->state=2;
		$this->x=$this->lMargin;
		$this->y=$this->tMargin;
		$this->FontFamily='';
		//Page orientation
		if(!$orientation)
			$orientation=$this->DefOrientation;
		else
		{
			$orientation=strtoupper($orientation{0});
			if($orientation!=$this->DefOrientation)
				$this->OrientationChanges[$this->page]=true;
		}
		if($orientation!=$this->CurOrientation)
		{
			//Change orientation
			if($orientation=='P')
			{
				$this->wPt=$this->fwPt;
				$this->hPt=$this->fhPt;
				$this->w=$this->fw;
				$this->h=$this->fh;
			}
			else
			{
				$this->wPt=$this->fhPt;
				$this->hPt=$this->fwPt;
				$this->w=$this->fh;
				$this->h=$this->fw;
			}
			$this->PageBreakTrigger=$this->h-$this->bMargin;
			$this->CurOrientation=$orientation;
		}
	}

	function _endpage()
	{
		//End of page contents
		$this->state=1;
	}

	function _newobj()
	{
		//Begin a new object
		$this->n++;
		$this->offsets[$this->n]=strlen($this->buffer);
		$this->_out($this->n.' 0 obj');
	}

	function _dounderline($x,$y,$txt)
	{
		//Underline text
		$up=$this->CurrentFont['up'];
		$ut=$this->CurrentFont['ut'];
		$w=$this->GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
		return sprintf('%.2f %.2f %.2f %.2f re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
	}

	function _parsejpg($file)
	{
		//Extract info from a JPEG file
		$a=GetImageSize($file);
		if(!$a)
			$this->Error('Missing or incorrect image file: '.$file);
		if($a[2]!=2)
			$this->Error('Not a JPEG file: '.$file);
		if(!isset($a['channels']) || $a['channels']==3)
			$colspace='DeviceRGB';
		elseif($a['channels']==4)
			$colspace='DeviceCMYK';
		else
			$colspace='DeviceGray';
		$bpc=isset($a['bits']) ? $a['bits'] : 8;
		//Read whole file
		$f=fopen($file,'rb');
		$data='';
		while(!feof($f))
			$data.=fread($f,4096);
		fclose($f);
		return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
	}

	function _parsepng($file)
	{
		//Extract info from a PNG file
		$f=fopen($file,'rb');
		if(!$f)
			$this->Error('Can\'t open image file: '.$file);
		//Check signature
		if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
			$this->Error('Not a PNG file: '.$file);
		//Read header chunk
		fread($f,4);
		if(fread($f,4)!='IHDR')
			$this->Error('Incorrect PNG file: '.$file);
		$w=$this->_freadint($f);
		$h=$this->_freadint($f);
		$bpc=ord(fread($f,1));
		if($bpc>8)
			$this->Error('16-bit depth not supported: '.$file);
		$ct=ord(fread($f,1));
		if($ct==0)
			$colspace='DeviceGray';
		elseif($ct==2)
			$colspace='DeviceRGB';
		elseif($ct==3)
			$colspace='Indexed';
		else
			$this->Error('Alpha channel not supported: '.$file);
		if(ord(fread($f,1))!=0)
			$this->Error('Unknown compression method: '.$file);
		if(ord(fread($f,1))!=0)
			$this->Error('Unknown filter method: '.$file);
		if(ord(fread($f,1))!=0)
			$this->Error('Interlacing not supported: '.$file);
		fread($f,4);
		$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
		//Scan chunks looking for palette, transparency and image data
		$pal='';
		$trns='';
		$data='';
		do
		{
			$n=$this->_freadint($f);
			$type=fread($f,4);
			if($type=='PLTE')
			{
				//Read palette
				$pal=fread($f,$n);
				fread($f,4);
			}
			elseif($type=='tRNS')
			{
				//Read transparency info
				$t=fread($f,$n);
				if($ct==0)
					$trns=array(ord(substr($t,1,1)));
				elseif($ct==2)
					$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
				else
				{
					$pos=strpos($t,chr(0));
					if($pos!==false)
						$trns=array($pos);
				}
				fread($f,4);
			}
			elseif($type=='IDAT')
			{
				//Read image data block
				$data.=fread($f,$n);
				fread($f,4);
			}
			elseif($type=='IEND')
				break;
			else
				fread($f,$n+4);
		}
		while($n);
		if($colspace=='Indexed' && empty($pal))
			$this->Error('Missing palette in '.$file);
		fclose($f);
		return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
	}

	function _freadint($f)
	{
		//Read a 4-byte integer from file
		$a=unpack('Ni',fread($f,4));
		return $a['i'];
	}

	function _textstring($s)
	{
		//Format a text string
		return '('.$this->_escape($s).')';
	}

	function _escape($s)
	{
		//Add \ before \, ( and )
		return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$s)));
	}

	function _putstream($s)
	{
		$this->_out('stream');
		$this->_out($s);
		$this->_out('endstream');
	}

	function _out($s)
	{
		//Add a line to the document
		if($this->state==2)
			$this->pages[$this->page].=$s."\n";
		else
			$this->buffer.=$s."\n";
	}
	//End of class
	}

	//Handle special IE contype request
	if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']=='contype')
	{
		header('Content-Type: application/pdf');
		exit;
	}

	}

	///////////////////////////////////////////////////////
	////				FPDF_TPL START  		  	   ////
	///////////////////////////////////////////////////////
	
	

	class FPDF_TPL extends FPDF {
	    /**
	     * Array of Tpl-Data
	     * @var array
	     */
	    var $tpls = array();

	    /**
	     * Current Template-ID
	     * @var int
	     */
	    var $tpl = 0;
	    
	    /**
	     * "In Template"-Flag
	     * @var boolean
	     */
	    var $_intpl = false;
	    
	    /**
	     * Nameprefix of Templates used in Resources-Dictonary
	     * @var string A String defining the Prefix used as Template-Object-Names. Have to beginn with an /
	     */
	    var $tplprefix = "/TPL";

	    /**
	     * Resources used By Templates and Pages
	     * @var array
	     */
	    var $_res = array();
	    
	    /**
	     * Constructor
	     * See FPDF-Documentation
	     * @param string $orientation
	     * @param string $unit
	     * @param mixed $format
	     */
	    function fpdf_tpl($orientation='P',$unit='mm',$format='A4') {
	        parent::fpdf($orientation,$unit,$format);
	    }
	    
	    /**
	     * Start a Template
	     *
	     * This method starts a template. You can give own coordinates to build an own sized
	     * Template. Pay attention, that the margins are adapted to the new templatesize.
	     * If you want to write outside the template, for example to build a clipped Template,
	     * you have to set the Margins and "Cursor"-Position manual after beginTemplate-Call.
	     *
	     * If no parameter is given, the template uses the current page-size.
	     * The Method returns an ID of the current Template. This ID is used later for using this template.
	     * Warning: A created Template is used in PDF at all events. Still if you don't use it after creation!
	     *
	     * @param int $x The x-coordinate given in user-unit
	     * @param int $y The y-coordinate given in user-unit
	     * @param int $w The width given in user-unit
	     * @param int $h The height given in user-unit
	     * @return int The ID of new created Template
	     */
	    function beginTemplate($x=null,$y=null,$w=null,$h=null) {
	        if ($this->page <= 0)
	            $this->error("You have to add a page to fpdf first!");

	        if ($x == null)
	            $x = 0;
	        if ($y == null)
	            $y = 0;
	        if ($w == null)
	            $w = $this->w;
	        if ($h == null)
	            $h = $this->h;

	        // Save settings
	        $this->tpl++;
	        $tpl =& $this->tpls[$this->tpl];
	        $tpl = array(
	            'o_x' => $this->x,
	            'o_y' => $this->y,
	            'o_AutoPageBreak' => $this->AutoPageBreak,
	            'o_bMargin' => $this->bMargin,
	            'o_tMargin' => $this->tMargin,
	            'o_lMargin' => $this->lMargin,
	            'o_rMargin' => $this->rMargin,
	            'o_h' => $this->h,
	            'o_w' => $this->w,
	            'buffer' => '',
	            'x' => $x,
	            'y' => $y,
	            'w' => $w,
	            'h' => $h
	        );

	        $this->SetAutoPageBreak(false);
	        
	        // Define own high and width to calculate possitions correct
	        $this->h = $h;
	        $this->w = $w;

	        $this->_intpl = true;
	        $this->SetXY($x+$this->lMargin,$y+$this->tMargin);
	        $this->SetRightMargin($this->w-$w+$this->rMargin);

	        return $this->tpl;
	    }
	    
	    /**
	     * End Template
	     *
	     * This method ends a template and reset initiated variables on beginTemplate.
	     *
	     * @return mixed If a template is opened, the ID is returned. If not a false is returned.
	     */
	    function endTemplate() {
	        if ($this->_intpl) {
	            $this->_intpl = false; 
	            $tpl =& $this->tpls[$this->tpl];
	            $this->SetXY($tpl['o_x'], $tpl['o_y']);
	            $this->tMargin = $tpl['o_tMargin'];
	            $this->lMargin = $tpl['o_lMargin'];
	            $this->rMargin = $tpl['o_rMargin'];
	            $this->h = $tpl['o_h'];
	            $this->w = $tpl['o_w'];
	            $this->SetAutoPageBreak($tpl['o_AutoPageBreak'], $tpl['o_bMargin']);
	            
	            return $this->tpl;
	        } else {
	            return false;
	        }
	    }
	    
	    /**
	     * Use a Template in current Page or other Template
	     *
	     * You can use a template in a page or in another template.
	     * You can give the used template a new size like you use the Image()-method.
	     * All parameters are optional. The width or height is calculated automaticaly
	     * if one is given. If no parameter is given the origin size as defined in
	     * beginTemplate() is used.
	     * The calculated or used width and height are returned as an array.
	     *
	     * @param int $tplidx A valid template-Id
	     * @param int $_x The x-position
	     * @param int $_y The y-position
	     * @param int $_w The new width of the template
	     * @param int $_h The new height of the template
	     * @retrun array The height and width of the template
	     */
	    function useTemplate($tplidx, $_x=null, $_y=null, $_w=0, $_h=0) {
	        if ($this->page <= 0)
	            $this->error("You have to add a page to fpdf first!");

	        if (!isset($this->tpls[$tplidx]))
	            $this->error("Template does not exist!");
	            
	        if ($this->_intpl) {
	            $this->_res['tpl'][$this->tpl]['tpls'][$tplidx] =& $this->tpls[$tplidx];
	        }
	        
	        $tpl =& $this->tpls[$tplidx];
	        $x = $tpl['x'];
	        $y = $tpl['y'];
	        $w = $tpl['w'];
	        $h = $tpl['h'];
	        
	        if ($_x == null)
	            $_x = $x;
	        if ($_y == null)
	            $_y = $y;
	        $wh = $this->getTemplateSize($tplidx,$_w,$_h);
	        $_w = $wh['w'];
	        $_h = $wh['h'];
	    
	        $this->_out(sprintf("q %.4f 0 0 %.4f %.2f %.2f cm", ($_w/$w), ($_h/$h), $_x*$this->k, ($this->h-($_y+$_h))*$this->k)); // Translate 
	        $this->_out($this->tplprefix.$tplidx." Do Q");

	        return array("w" => $_w, "h" => $_h);
	    }
	    
	    /**
	     * Get The calculated Size of a Template
	     *
	     * If one size is given, this method calculates the other one.
	     *
	     * @param int $tplidx A valid template-Id
	     * @param int $_w The width of the template
	     * @param int $_h The height of the template
	     * @return array The height and width of the template
	     */
	    function getTemplateSize($tplidx, $_w=0, $_h=0) {
	        if (!$this->tpls[$tplidx])
	            return false;

	        $tpl =& $this->tpls[$tplidx];
	        $w = $tpl['w'];
	        $h = $tpl['h'];
	        
	        if ($_w == 0 and $_h == 0) {
	            $_w = $w;
	            $_h = $h;
	        }

	    	if($_w==0)
	    		$_w=$_h*$w/$h;
	    	if($_h==0)
	    		$_h=$_w*$h/$w;
	    		
	        return array("w" => $_w, "h" => $_h);
	    }
	    
	    /**
	     * See FPDF-Documentation ;-)
	     */
	    function SetFont($family,$style='',$size=0) {
	        /**
	         * force the resetting of font changes in a template
	         */
	        if ($this->_intpl)
	            $this->FontFamily = '';
	            
	        parent::SetFont($family, $style, $size);
	       
	        $fontkey = $this->FontFamily.$this->FontStyle;
	        
	        if ($this->_intpl) {
	            $this->_res['tpl'][$this->tpl]['fonts'][$fontkey] =& $this->fonts[$fontkey];
	        } else {
	            $this->_res['page'][$this->page]['fonts'][$fontkey] =& $this->fonts[$fontkey];
	        }
	    }
	    
	    /**
	     * See FPDF-Documentation ;-)
	     */
	    function Image($file,$x,$y,$w=0,$h=0,$type='',$link='') {
	        parent::Image($file,$x,$y,$w,$h,$type,$link);
	        if ($this->_intpl) {
	            $this->_res['tpl'][$this->tpl]['images'][$file] =& $this->images[$file];
	        } else {
	            $this->_res['page'][$this->page]['images'][$file] =& $this->images[$file];
	        }
	    }
	    
	    /**
	     * See FPDF-Documentation ;-)
	     *
	     * AddPage is not available when you're "in" a template.
	     */
	    function AddPage($orientation='') {
	        if ($this->_intpl)
	            $this->Error('Adding pages in templates isn\'t possible!');
	        parent::AddPage($orientation);
	    }

	    /**
	     * Preserve adding Links in Templates ...won't work
	     */
	    function Link($x,$y,$w,$h,$link) {
	        if ($this->_intpl)
	            $this->Error('Using links in templates aren\'t possible!');
	        parent::Link($x,$y,$w,$h,$link);
	    }
	    
	    function AddLink() {
	        if ($this->_intpl)
	            $this->Error('Adding links in templates aren\'t possible!');
	        return parent::AddLink();
	    }
	    
	    function SetLink($link,$y=0,$page=-1) {
	        if ($this->_intpl)
	            $this->Error('Setting links in templates aren\'t possible!');
	        parent::SetLink($link,$y,$page);
	    }
	    
	    /**
	     * Private Method that writes the form xobjects
	     */
	    function _putformxobjects() {
	        $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		    reset($this->tpls);
	        foreach($this->tpls AS $tplidx => $tpl) {

	            $p=($this->compress) ? gzcompress($tpl['buffer']) : $tpl['buffer'];
	    		$this->_newobj();
	    		$this->tpls[$tplidx]['n'] = $this->n;
	    		$this->_out('<<'.$filter.'/Type /XObject');
	            $this->_out('/Subtype /Form');
	            $this->_out('/FormType 1');
	            $this->_out(sprintf('/BBox [%.2f %.2f %.2f %.2f]',$tpl['x']*$this->k, ($tpl['h']-$tpl['y'])*$this->k, $tpl['w']*$this->k, ($tpl['h']-$tpl['y']-$tpl['h'])*$this->k));
	            $this->_out('/Resources ');

	            $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
	        	if (isset($this->_res['tpl'][$tplidx]['fonts']) && count($this->_res['tpl'][$tplidx]['fonts'])) {
	            	$this->_out('/Font <<');
	                foreach($this->_res['tpl'][$tplidx]['fonts'] as $font)
	            		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
	            	$this->_out('>>');
	            }
	        	if(isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images']) || 
	        	   isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls']))
	        	{
	                $this->_out('/XObject <<');
	                if (isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images'])) {
	                    foreach($this->_res['tpl'][$tplidx]['images'] as $image)
	              			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
	                }
	                if (isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls'])) {
	                    foreach($this->_res['tpl'][$tplidx]['tpls'] as $i => $tpl)
	                        $this->_out($this->tplprefix.$i.' '.$tpl['n'].' 0 R');
	                }
	                $this->_out('>>');
	        	}
	        	$this->_out('>>');
	        	
	        	$this->_out('/Length '.strlen($p).' >>');
	    		$this->_putstream($p);
	    		$this->_out('endobj');
	        }
	    }
	    
	    /**
	     * Private Method
	     */
	    function _putresources() {
	        $this->_putfonts();
	    	$this->_putimages();
	    	$this->_putformxobjects();
	        //Resource dictionary
	    	$this->offsets[2]=strlen($this->buffer);
	    	$this->_out('2 0 obj');
	    	$this->_out('<<');
	    	$this->_putresourcedict();
	    	$this->_out('>>');
	    	$this->_out('endobj');
	    }
	    
	    function _putxobjectdict() {
	        parent::_putxobjectdict();
	        
	        if (count($this->tpls)) {
	            foreach($this->tpls as $tplidx => $tpl) {
	                $this->_out($this->tplprefix.$tplidx.' '.$tpl['n'].' 0 R');
	            }
	        }
	    }

	    /**
	     * Private Method
	     */
	    function _out($s) {
		   //Add a line to the document
		   if ($this->state==2) {
	           if (!$this->_intpl)
		           $this->pages[$this->page].=$s."\n";
	           else
	               $this->tpls[$this->tpl]['buffer'] .= $s."\n";
	       } else {
			   $this->buffer.=$s."\n";
	       }
	    }
	}

	///////////////////////////////////////////////////////
	////				PDF_CONTEXT START 		  	   ////
	///////////////////////////////////////////////////////

	class pdf_context {

		var $file;
		var $buffer;
		var $offset;
		var $length;

		var $stack;

		// Constructor

		function pdf_context($f) {
			$this->file = $f;
			$this->reset();
		}

		// Optionally move the file
		// pointer to a new location
		// and reset the buffered data

		function reset($pos = null, $l = 100) {
			if (!is_null ($pos)) {
				fseek ($this->file, $pos);
			}

			$this->buffer = $l > 0 ? fread($this->file, $l) : '';
			$this->offset = 0;
			$this->length = strlen($this->buffer);
			$this->stack = array();
		}

		// Make sure that there is at least one
		// character beyond the current offset in
		// the buffer to prevent the tokenizer
		// from attempting to access data that does
		// not exist

		function ensure_content() {
			if ($this->offset >= $this->length - 1) {
				return $this->increase_length();
			} else {
				return true;
			}
		}

		// Forcefully read more data into the buffer

		function increase_length($l=100) {
			if (feof($this->file)) {
				return false;
			} else {
				$this->buffer .= fread($this->file, $l);
				$this->length = strlen($this->buffer);
				return true;
			}
		}

	}

	///////////////////////////////////////////////////////
	////			WRAPPER_FUNCTION START 		  	   ////
	///////////////////////////////////////////////////////

	if (!defined("PHP_VER_LOWER43")) 
		define("PHP_VER_LOWER43", version_compare(PHP_VERSION, "4.3", "<"));


	/**
	 * ensure that strspn works correct if php-version < 4.3
	 */
	function _strspn($str1, $str2, $start=null, $length=null) {
	    $numargs = func_num_args();

	    if (PHP_VER_LOWER43 == 1) {
	        if (isset($length)) {
	            $str1 = substr($str1, $start, $length);
	        } else {
	            $str1 = substr($str1, $start);
	        }
	    }

	    if ($numargs == 2 || PHP_VER_LOWER43 == 1) {
	        return strspn($str1, $str2);
	    } else if ($numargs == 3) {
	        return strspn($str1, $str2, $start);
	    } else {
	        return strspn($str1, $str2, $start, $length);
	    }
	}


	/**
	 * ensure that strcspn works correct if php-version < 4.3
	 */
	function _strcspn($str1, $str2, $start=null, $length=null) {
	    $numargs = func_num_args();

	    if (PHP_VER_LOWER43 == 1) {
	        if (isset($length)) {
	            $str1 = substr($str1, $start, $length);
	        } else {
	            $str1 = substr($str1, $start);
	        }
	    }

	    if ($numargs == 2 || PHP_VER_LOWER43 == 1) {
	        return strcspn($str1, $str2);
	    } else if ($numargs == 3) {
	        return strcspn($str1, $str2, $start);
	    } else {
	        return strcspn($str1, $str2, $start, $length);
	    }
	}


	/**
	 * ensure that fgets works correct if php-version < 4.3
	 */
	function _fgets (&$h, $force=false) {
	    $startpos = ftell($h);
		$s = fgets($h, 1024);
	    
	    if ((PHP_VER_LOWER43 == 1 || $force) && preg_match("/^([^\r\n]*[\r\n]{1,2})(.)/",trim($s), $ns)) {
			$s = $ns[1];
			fseek($h,$startpos+strlen($s));
		}
		
		return $s;
	}

	///////////////////////////////////////////////////////
	////				PDF_PARSER START 		  	   ////
	///////////////////////////////////////////////////////

	
	if (!defined ('PDF_TYPE_NULL'))
	    define ('PDF_TYPE_NULL', 0);
	if (!defined ('PDF_TYPE_NUMERIC'))
	    define ('PDF_TYPE_NUMERIC', 1);
	if (!defined ('PDF_TYPE_TOKEN'))
	    define ('PDF_TYPE_TOKEN', 2);
	if (!defined ('PDF_TYPE_HEX'))
	    define ('PDF_TYPE_HEX', 3);
	if (!defined ('PDF_TYPE_STRING'))
	    define ('PDF_TYPE_STRING', 4);
	if (!defined ('PDF_TYPE_DICTIONARY'))
	    define ('PDF_TYPE_DICTIONARY', 5);
	if (!defined ('PDF_TYPE_ARRAY'))
	    define ('PDF_TYPE_ARRAY', 6);
	if (!defined ('PDF_TYPE_OBJDEC'))
	    define ('PDF_TYPE_OBJDEC', 7);
	if (!defined ('PDF_TYPE_OBJREF'))
	    define ('PDF_TYPE_OBJREF', 8);
	if (!defined ('PDF_TYPE_OBJECT'))
	    define ('PDF_TYPE_OBJECT', 9);
	if (!defined ('PDF_TYPE_STREAM'))
	    define ('PDF_TYPE_STREAM', 10);


	class pdf_parser {
		
		/**
	     * Filename
	     * @var string
	     */
	    var $filename;
	    
	    /**
	     * File resource
	     * @var resource
	     */
	    var $f;
	    
	    /**
	     * PDF Context
	     * @var object pdf_context-Instance
	     */
	    var $c;
	    
	    /**
	     * xref-Data
	     * @var array
	     */
	    var $xref;

	    /**
	     * root-Object
	     * @var array
	     */
	    var $root;
		
	    
	    /**
	     * Constructor
	     *
	     * @param string $filename  Source-Filename
	     */
		function pdf_parser($filename) {
	        $this->filename = $filename;

	        $this->f = @fopen($this->filename, "rb");
	        				  //../ppsv2/uploads/Fonetik_Fonologi.pdf
	        //$this->f = fopen('../ppsv2/uploads/Fonetik_Fonologi.pdf', "rb");
	        //var_dump();
	        //ar_dump(file_get_contents($this->filename));
	        //var_dump($this->f);
	        if (!$this->f)
	            $this->error(sprintf("Cannot open %s !", $filename));

	        $this->getPDFVersion();

	        $this->c = new pdf_context($this->f);
	        // Read xref-Data
	        $this->pdf_read_xref($this->xref, $this->pdf_find_xref());

	        // Check for Encryption
	        $this->getEncryption();

	        // Read root
	        $this->pdf_read_root();
	    }
	    
	    /**
	     * Close the opened file
	     */
	    function closeFile() {
	    	if (isset($this->f)) {
	    	    fclose($this->f);	
	    		unset($this->f);
	    	}	
	    }
	    
	    /**
	     * Print Error and die
	     *
	     * @param string $msg  Error-Message
	     */
	    function error($msg) {
	    	die("<b>PDF-Parser Error:</b> ".$msg);	
	    }
	    
	    /**
	     * Check Trailer for Encryption
	     */
	    function getEncryption() {
	        if (isset($this->xref['trailer'][1]['/Encrypt'])) {
	            $this->error("File is encrypted!");
	        }
	    }
	    
		/**
	     * Find/Return /Root
	     *
	     * @return array
	     */
	    function pdf_find_root() {
	        if ($this->xref['trailer'][1]['/Root'][0] != PDF_TYPE_OBJREF) {
	            $this->error("Wrong Type of Root-Element! Must be an indirect reference");
	        }
	        return $this->xref['trailer'][1]['/Root'];
	    }

	    /**
	     * Read the /Root
	     */
	    function pdf_read_root() {
	        // read root
	        $this->root = $this->pdf_resolve_object($this->c, $this->pdf_find_root());
	    }
	    
	    /**
	     * Get PDF-Version
	     *
	     * And reset the PDF Version used in FPDI if needed
	     */
	    function getPDFVersion() {
	        fseek($this->f, 0);
	        preg_match("/\d\.\d/",fread($this->f,16),$m);
	        $this->pdfVersion = $m[0];
	    }
	    
	    /**
	     * Find the xref-Table
	     */
	    function pdf_find_xref() {
	       	fseek ($this->f, -min(filesize($this->filename),1500), SEEK_END);
	        $data = fread($this->f, 1500);
	        
	        $pos = strlen($data) - strpos(strrev($data), strrev('startxref')); 
	        $data = substr($data, $pos);
	        
	        if (!preg_match('/\s*(\d+).*$/s', $data, $matches)) {
	            $this->error("Unable to find pointer to xref table");
	    	}

	    	return (int) $matches[1];
	    }

	    /**
	     * Read xref-table
	     *
	     * @param array $result Array of xref-table
	     * @param integer $offset of xref-table
	     * @param integer $start start-position in xref-table
	     * @param integer $end end-position in xref-table
	     */
	    function pdf_read_xref(&$result, $offset, $start = null, $end = null) {
	        if (is_null ($start) || is_null ($end)) {
			    fseek($this->f, $o_pos = $offset);
	            $data = trim(fgets($this->f,1024));
	            	        
	            if (strlen($data) == 0) 
	                $data = trim(fgets($this->f,1024));
	            		
	            if ($data !== 'xref') {
	            	fseek($this->f, $o_pos);
	            	$data = trim(_fgets($this->f, true));
	            	if ($data !== 'xref') {
	            	    if (preg_match('/(.*xref)(.*)/m', $data, $m)) { // xref 0 128 - in one line
	                        fseek($this->f, $o_pos+strlen($m[1]));            	        
	            	    } elseif (preg_match('/(x|r|e|f)+/', $data, $m)) { // correct invalid xref-pointer
	            	        $tmpOffset = $offset-4+strlen($m[0]);
	            	        $this->pdf_read_xref($result, $tmpOffset, $start, $end);
	            	        return;
	                    } else {
	                        $this->error("Unable to find xref table - Maybe a Problem with 'auto_detect_line_endings'");
	            	    }
	            	}
	    		}

	    		$o_pos = ftell($this->f);
	    	    $data = explode(' ', trim(fgets($this->f,1024)));
				if (count($data) != 2) {
	    	        fseek($this->f, $o_pos);
	    	        $data = explode(' ', trim(_fgets($this->f, true)));
				
	            	if (count($data) != 2) {
	            	    if (count($data) > 2) { // no lineending
	            	        $n_pos = $o_pos+strlen($data[0])+strlen($data[1])+2;
	            	        fseek($this->f, $n_pos);
	            	    } else {
	                        $this->error("Unexpected header in xref table");
	            	    }
	            	}
	            }
	            $start = $data[0];
	            $end = $start + $data[1];
	        }

	        if (!isset($result['xref_location'])) {
	            $result['xref_location'] = $offset;
	    	}

	    	if (!isset($result['max_object']) || $end > $result['max_object']) {
	    	    $result['max_object'] = $end;
	    	}

	    	for (; $start < $end; $start++) {
	    		$data = ltrim(fread($this->f, 20)); // Spezifications says: 20 bytes including newlines
	    		$offset = substr($data, 0, 10);
	    		$generation = substr($data, 11, 5);

	    	    if (!isset ($result['xref'][$start][(int) $generation])) {
	    	    	$result['xref'][$start][(int) $generation] = (int) $offset;
	    	    }
	    	}

	    	$o_pos = ftell($this->f);
	        $data = fgets($this->f,1024);
			if (strlen(trim($data)) == 0) 
			    $data = fgets($this->f, 1024);
	        
	        if (preg_match("/trailer/",$data)) {
	            if (preg_match("/(.*trailer[ \n\r]*)/",$data,$m)) {
	            	fseek($this->f, $o_pos+strlen($m[1]));
	    		}
	    		
				$c =  new pdf_context($this->f);
	    	    $trailer = $this->pdf_read_value($c);
	    	    
	    	    if (isset($trailer[1]['/Prev'])) {
	    	    	$this->pdf_read_xref($result, $trailer[1]['/Prev'][1]);
	    		    $result['trailer'][1] = array_merge($result['trailer'][1], $trailer[1]);
	    	    } else {
	    	        $result['trailer'] = $trailer;
	            }
	    	} else {
	    	    $data = explode(' ', trim($data));
	            
	    		if (count($data) != 2) {
	            	fseek($this->f, $o_pos);
	        		$data = explode(' ', trim (_fgets ($this->f, true)));

	        		if (count($data) != 2) {
	        		    $this->error("Unexpected data in xref table");
	        		}
			    }
			    
			    $this->pdf_read_xref($result, null, (int) $data[0], (int) $data[0] + (int) $data[1]);
	    	}
	    }


	    /**
	     * Reads an Value
	     *
	     * @param object $c pdf_context
	     * @param string $token a Token
	     * @return mixed
	     */
	    function pdf_read_value(&$c, $token = null) {
	    	if (is_null($token)) {
	    	    $token = $this->pdf_read_token($c);
	    	}
	    	
	        if ($token === false) {
	    	    return false;
	    	}

	       	switch ($token) {
	            case	'<':
	    			// This is a hex string.
	    			// Read the value, then the terminator

	                $pos = $c->offset;

	    			while(1) {

	                    $match = strpos ($c->buffer, '>', $pos);
					
	    				// If you can't find it, try
	    				// reading more data from the stream

	    				if ($match === false) {
	    					if (!$c->increase_length()) {
	    						return false;
	    					} else {
	                        	continue;
	                    	}
	    				}

	    				$result = substr ($c->buffer, $c->offset, $match - $c->offset);
	    				$c->offset = $match+1;
	    				
	    				return array (PDF_TYPE_HEX, $result);
	                }
	                
	                break;
	    		case	'<<':
	    			// This is a dictionary.

	    			$result = array();

	    			// Recurse into this function until we reach
	    			// the end of the dictionary.
	    			while (($key = $this->pdf_read_token($c)) !== '>>') {
	    				if ($key === false) {
	    					return false;
	    				}
						
	    				if (($value =   $this->pdf_read_value($c)) === false) {
	    					return false;
	    				}
	                    $result[$key] = $value;
	    			}
					
	    			return array (PDF_TYPE_DICTIONARY, $result);

	    		case	'[':
	    			// This is an array.

	    			$result = array();

	    			// Recurse into this function until we reach
	    			// the end of the array.
	    			while (($token = $this->pdf_read_token($c)) !== ']') {
	                    if ($token === false) {
	    					return false;
	    				}
						
	    				if (($value = $this->pdf_read_value($c, $token)) === false) {
	                        return false;
	    				}
						
	    				$result[] = $value;
	    			}
	    			
	                return array (PDF_TYPE_ARRAY, $result);

	    		case	'('		:
	                // This is a string

	    			$pos = $c->offset;

	    			while(1) {

	                    // Start by finding the next closed
	    				// parenthesis

	    				$match = strpos ($c->buffer, ')', $pos);

	    				// If you can't find it, try
	    				// reading more data from the stream

	    				if ($match === false) {
	    					if (!$c->increase_length()) {
	                            return false;
	    					} else {
	                            continue;
	                        }
	    				}

	    				// Make sure that there is no backslash
	    				// before the parenthesis. If there is,
	    				// move on. Otherwise, return the string.
	                    $esc = preg_match('/([\\\\]+)$/', $tmpresult = substr($c->buffer, $c->offset, $match - $c->offset), $m);
	                    
	                    if ($esc === 0 || strlen($m[1]) % 2 == 0) {
	    				    $result = $tmpresult;
	                        $c->offset = $match + 1;
	                        return array (PDF_TYPE_STRING, $result);
	    				} else {
	    					$pos = $match + 1;

	    					if ($pos > $c->offset + $c->length) {
	    						$c->increase_length();
	    					}
	    				}    				
	                }

	            case "stream":
	            	$o_pos = ftell($c->file)-strlen($c->buffer);
			        $o_offset = $c->offset;
			        
			        $c->reset($startpos = $o_pos + $o_offset);
			        
			        $e = 0; // ensure line breaks in front of the stream
			        if ($c->buffer[0] == chr(10) || $c->buffer[0] == chr(13))
			        	$e++;
			        if ($c->buffer[1] == chr(10) && $c->buffer[0] != chr(10))
			        	$e++;
			        
			        if ($this->actual_obj[1][1]['/Length'][0] == PDF_TYPE_OBJREF) {
			        	$tmp_c = new pdf_context($this->f);
			        	$tmp_length = $this->pdf_resolve_object($tmp_c,$this->actual_obj[1][1]['/Length']);
			        	$length = $tmp_length[1][1];
			        } else {
			        	$length = $this->actual_obj[1][1]['/Length'][1];	
			        }
			        
			        if ($length > 0) {
	    		        $c->reset($startpos+$e,$length);
	    		        $v = $c->buffer;
			        } else {
			            $v = '';   
			        }
			        $c->reset($startpos+$e+$length+9); // 9 = strlen("endstream")
			        
			        return array(PDF_TYPE_STREAM, $v);
			        
	    		default	:
	            	if (is_numeric ($token)) {
	                    // A numeric token. Make sure that
	    				// it is not part of something else.
	    				if (($tok2 = $this->pdf_read_token ($c)) !== false) {
	                        if (is_numeric ($tok2)) {

	    						// Two numeric tokens in a row.
	    						// In this case, we're probably in
	    						// front of either an object reference
	    						// or an object specification.
	    						// Determine the case and return the data
	    						if (($tok3 = $this->pdf_read_token ($c)) !== false) {
	                                switch ($tok3) {
	    								case	'obj'	:
	                                        return array (PDF_TYPE_OBJDEC, (int) $token, (int) $tok2);
	    								case	'R'		:
	    									return array (PDF_TYPE_OBJREF, (int) $token, (int) $tok2);
	    							}
	    							// If we get to this point, that numeric value up
	    							// there was just a numeric value. Push the extra
	    							// tokens back into the stack and return the value.
	    							array_push ($c->stack, $tok3);
	    						}
	    					}

	    					array_push ($c->stack, $tok2);
	    				}

	    				return array (PDF_TYPE_NUMERIC, $token);
	    			} else {

	                    // Just a token. Return it.
	    				return array (PDF_TYPE_TOKEN, $token);
	    			}

	         }
	    }
	    
	    /**
	     * Resolve an object
	     *
	     * @param object $c pdf_context
	     * @param array $obj_spec The object-data
	     * @param boolean $encapsulate Must set to true, cause the parsing and fpdi use this method only without this para
	     */
	    function pdf_resolve_object(&$c, $obj_spec, $encapsulate = true) {
	        // Exit if we get invalid data
	    	if (!is_array($obj_spec)) {
	            return false;
	    	}

	    	if ($obj_spec[0] == PDF_TYPE_OBJREF) {

	    		// This is a reference, resolve it
	    		if (isset($this->xref['xref'][$obj_spec[1]][$obj_spec[2]])) {

	    			// Save current file position
	    			// This is needed if you want to resolve
	    			// references while you're reading another object
	    			// (e.g.: if you need to determine the length
	    			// of a stream)

	    			$old_pos = ftell($c->file);

	    			// Reposition the file pointer and
	    			// load the object header.
					
	    			$c->reset($this->xref['xref'][$obj_spec[1]][$obj_spec[2]]);

	    			$header = $this->pdf_read_value($c,null,true);

	    			if ($header[0] != PDF_TYPE_OBJDEC || $header[1] != $obj_spec[1] || $header[2] != $obj_spec[2]) {
	    				$this->error("Unable to find object ({$obj_spec[1]}, {$obj_spec[2]}) at expected location");
	    			}

	    			// If we're being asked to store all the information
	    			// about the object, we add the object ID and generation
	    			// number for later use
					$this->actual_obj =& $result;
	    			if ($encapsulate) {
	    				$result = array (
	    					PDF_TYPE_OBJECT,
	    					'obj' => $obj_spec[1],
	    					'gen' => $obj_spec[2]
	    				);
	    			} else {
	    				$result = array();
	    			}

	    			// Now simply read the object data until
	    			// we encounter an end-of-object marker
	    			while(1) {
	                    $value = $this->pdf_read_value($c);
						if ($value === false || count($result) > 4) {
							// in this case the parser coudn't find an endobj so we break here
							break;
	    				}

	    				if ($value[0] == PDF_TYPE_TOKEN && $value[1] === 'endobj') {
	    					break;
	    				}

	                    $result[] = $value;
	    			}

	    			$c->reset($old_pos);

	                if (isset($result[2][0]) && $result[2][0] == PDF_TYPE_STREAM) {
	                    $result[0] = PDF_TYPE_STREAM;
	                }

	    			return $result;
	    		}
	    	} else {
	    		return $obj_spec;
	    	}
	    }

	    
	    
	    /**
	     * Reads a token from the file
	     *
	     * @param object $c pdf_context
	     * @return mixed
	     */
	    function pdf_read_token(&$c)
	    {
	    	// If there is a token available
	    	// on the stack, pop it out and
	    	// return it.

	    	if (count($c->stack)) {
	    		return array_pop($c->stack);
	    	}

	    	// Strip away any whitespace

	    	do {
	    		if (!$c->ensure_content()) {
	    			return false;
	    		}
	    		$c->offset += _strspn($c->buffer, " \n\r\t", $c->offset);
	    	} while ($c->offset >= $c->length - 1);

	    	// Get the first character in the stream

	    	$char = $c->buffer[$c->offset++];

	    	switch ($char) {

	    		case '['	:
	    		case ']'	:
	    		case '('	:
	    		case ')'	:

	    			// This is either an array or literal string
	    			// delimiter, Return it

	    			return $char;

	    		case '<'	:
	    		case '>'	:

	    			// This could either be a hex string or
	    			// dictionary delimiter. Determine the
	    			// appropriate case and return the token

	    			if ($c->buffer[$c->offset] == $char) {
	    				if (!$c->ensure_content()) {
	    				    return false;
	    				}
	    				$c->offset++;
	    				return $char . $char;
	    			} else {
	    				return $char;
	    			}

	    		default		:

	    			// This is "another" type of token (probably
	    			// a dictionary entry or a numeric value)
	    			// Find the end and return it.

	    			if (!$c->ensure_content()) {
	    				return false;
	    			}

	    			while(1) {

	    				// Determine the length of the token

	    				$pos = _strcspn($c->buffer, " []<>()\r\n\t/", $c->offset);
	    				if ($c->offset + $pos <= $c->length - 1) {
	    					break;
	    				} else {
	    					// If the script reaches this point,
	    					// the token may span beyond the end
	    					// of the current buffer. Therefore,
	    					// we increase the size of the buffer
	    					// and try again--just to be safe.

	    					$c->increase_length();
	    				}
	    			}

	    			$result = substr($c->buffer, $c->offset - 1, $pos + 1);

	    			$c->offset += $pos;
	    			return $result;
	    	}
	    }

		
	}

	///////////////////////////////////////////////////////
	////			FPDI_PDF_PARSER START 		  	   ////
	///////////////////////////////////////////////////////

	

	class fpdi_pdf_parser extends pdf_parser {

	    /**
	     * Pages
	     * Index beginns at 0
	     *
	     * @var array
	     */
	    var $pages;
	    
	    /**
	     * Page count
	     * @var integer
	     */
	    var $page_count;
	    
	    /**
	     * actual page number
	     * @var integer
	     */
	    var $pageno;
	    
	    /**
	     * PDF Version of imported Document
	     * @var string
	     */
	    var $pdfVersion;
	    
	    /**
	     * FPDI Reference
	     * @var object
	     */
	    var $fpdi;
	    
	    /**
	     * Available BoxTypes
	     *
	     * @var array
	     */
	    var $availableBoxes = array("/MediaBox","/CropBox","/BleedBox","/TrimBox","/ArtBox");
	        
	    /**
	     * Constructor
	     *
	     * @param string $filename  Source-Filename
	     * @param object $fpdi      Object of type fpdi
	     */
	    function fpdi_pdf_parser($filename,&$fpdi) {
	        $this->fpdi =& $fpdi;
			$this->filename = $filename;
			
	        parent::pdf_parser($filename);

	        // resolve Pages-Dictonary
	        $pages = $this->pdf_resolve_object($this->c, $this->root[1][1]['/Pages']);

	        // Read pages
	        $this->read_pages($this->c, $pages, $this->pages);
	        
	        // count pages;
	        $this->page_count = count($this->pages);
	    }
	    
	    /**
	     * Overwrite parent::error()
	     *
	     * @param string $msg  Error-Message
	     */
	    function error($msg) {
	    	$this->fpdi->error($msg);	
	    }
	    
	    /**
	     * Get pagecount from sourcefile
	     *
	     * @return int
	     */
	    function getPageCount() {
	        return $this->page_count;
	    }


	    /**
	     * Set pageno
	     *
	     * @param int $pageno Pagenumber to use
	     */
	    function setPageno($pageno) {
	        $pageno = ((int) $pageno) - 1;

	        if ($pageno < 0 || $pageno >= $this->getPageCount()) {
	            $this->fpdi->error("Pagenumber is wrong!");
	        }

	        $this->pageno = $pageno;
	    }
	    
	    /**
	     * Get page-resources from current page
	     *
	     * @return array
	     */
	    function getPageResources() {
	        return $this->_getPageResources($this->pages[$this->pageno]);
	    }
	    
	    /**
	     * Get page-resources from /Page
	     *
	     * @param array $obj Array of pdf-data
	     */
	    function _getPageResources ($obj) { // $obj = /Page
	    	$obj = $this->pdf_resolve_object($this->c, $obj);

	        // If the current object has a resources
	    	// dictionary associated with it, we use
	    	// it. Otherwise, we move back to its
	    	// parent object.
	        if (isset ($obj[1][1]['/Resources'])) {
	    		$res = $this->pdf_resolve_object($this->c, $obj[1][1]['/Resources']);
	    		if ($res[0] == PDF_TYPE_OBJECT)
	                return $res[1];
	            return $res;
	    	} else {
	    		if (!isset ($obj[1][1]['/Parent'])) {
	    			return false;
	    		} else {
	                $res = $this->_getPageResources($obj[1][1]['/Parent']);
	                if ($res[0] == PDF_TYPE_OBJECT)
	                    return $res[1];
	                return $res;
	    		}
	    	}
	    }


	    /**
	     * Get content of current page
	     *
	     * If more /Contents is an array, the streams are concated
	     *
	     * @return string
	     */
	    function getContent() {
	        $buffer = "";
	        
	        if (isset($this->pages[$this->pageno][1][1]['/Contents'])) {
	            $contents = $this->_getPageContent($this->pages[$this->pageno][1][1]['/Contents']);
	            foreach($contents AS $tmp_content) {
	                $buffer .= $this->_rebuildContentStream($tmp_content).' ';
	            }
	        }
	        
	        return $buffer;
	    }
	    
	    
	    /**
	     * Resolve all content-objects
	     *
	     * @param array $content_ref
	     * @return array
	     */
	    function _getPageContent($content_ref) {
	        $contents = array();
	        
	        if ($content_ref[0] == PDF_TYPE_OBJREF) {
	            $content = $this->pdf_resolve_object($this->c, $content_ref);
	            if ($content[1][0] == PDF_TYPE_ARRAY) {
	                $contents = $this->_getPageContent($content[1]);
	            } else {
	                $contents[] = $content;
	            }
	        } else if ($content_ref[0] == PDF_TYPE_ARRAY) {
	            foreach ($content_ref[1] AS $tmp_content_ref) {
	                $contents = array_merge($contents,$this->_getPageContent($tmp_content_ref));
	            }
	        }

	        return $contents;
	    }


	    /**
	     * Rebuild content-streams
	     *
	     * @param array $obj
	     * @return string
	     */
	    function _rebuildContentStream($obj) {
	        $filters = array();
	        
	        if (isset($obj[1][1]['/Filter'])) {
	            $_filter = $obj[1][1]['/Filter'];

	            if ($_filter[0] == PDF_TYPE_TOKEN) {
	                $filters[] = $_filter;
	            } else if ($_filter[0] == PDF_TYPE_ARRAY) {
	                $filters = $_filter[1];
	            }
	        }

	        $stream = $obj[2][1];

	        foreach ($filters AS $_filter) {
	            switch ($_filter[1]) {
	                case "/FlateDecode":
	                    if (function_exists('gzuncompress')) {
	                        $stream = (strlen($stream) > 0) ? @gzuncompress($stream) : '';                        
	                    } else {
	                        $this->fpdi->error(sprintf("To handle %s filter, please compile php with zlib support.",$_filter[1]));
	                    }
	                    if ($stream === false) {
	                        $this->fpdi->error("Error while decompressing stream.");
	                    }
	                break;
	                case null:
	                    $stream = $stream;
	                break;
	                default:
	                    if (preg_match("/^\/[a-z85]*$/i", $_filter[1], $filterName) && @include_once('decoders'.$_filter[1].'.php')) {
	                        $filterName = substr($_filter[1],1);
	                        if (class_exists($filterName)) {
	    	                	$decoder = new $filterName($this->fpdi);
	    	                    $stream = $decoder->decode(trim($stream));
	                        } else {
	                        	$this->fpdi->error(sprintf("Unsupported Filter: %s",$_filter[1]));
	                        }
	                    } else {
	                        $this->fpdi->error(sprintf("Unsupported Filter: %s",$_filter[1]));
	                    }
	            }
	        }
	        
	        return $stream;
	    }
	    
	    
	    /**
	     * Get a Box from a page
	     * Arrayformat is same as used by fpdf_tpl
	     *
	     * @param array $page a /Page
	     * @param string $box_index Type of Box @see $availableBoxes
	     * @return array
	     */
	    function getPageBox($page, $box_index) {
	        $page = $this->pdf_resolve_object($this->c,$page);
	        $box = null;
	        if (isset($page[1][1][$box_index]))
	            $box =& $page[1][1][$box_index];
	        
	        if (!is_null($box) && $box[0] == PDF_TYPE_OBJREF) {
	            $tmp_box = $this->pdf_resolve_object($this->c,$box);
	            $box = $tmp_box[1];
	        }
	            
	        if (!is_null($box) && $box[0] == PDF_TYPE_ARRAY) {
	            $b =& $box[1];
	            return array("x" => $b[0][1]/$this->fpdi->k,
	                         "y" => $b[1][1]/$this->fpdi->k,
	                         "w" => abs($b[0][1]-$b[2][1])/$this->fpdi->k,
	                         "h" => abs($b[1][1]-$b[3][1])/$this->fpdi->k);
	        } else if (!isset ($page[1][1]['/Parent'])) {
	            return false;
	        } else {
	            return $this->getPageBox($this->pdf_resolve_object($this->c, $page[1][1]['/Parent']), $box_index);
	        }
	    }

	    function getPageBoxes($pageno) {
	        return $this->_getPageBoxes($this->pages[$pageno-1]);
	    }
	    
	    /**
	     * Get all Boxes from /Page
	     *
	     * @param array a /Page
	     * @return array
	     */
	    function _getPageBoxes($page) {
	        $boxes = array();

	        foreach($this->availableBoxes AS $box) {
	            if ($_box = $this->getPageBox($page,$box)) {
	                $boxes[$box] = $_box;
	            }
	        }

	        return $boxes;
	    }

	    function getPageRotation($pageno) {
	        return $this->_getPageRotation($this->pages[$pageno-1]);
	    }
	    
	    function _getPageRotation ($obj) { // $obj = /Page
	    	$obj = $this->pdf_resolve_object($this->c, $obj);
	    	if (isset ($obj[1][1]['/Rotate'])) {
	    		$res = $this->pdf_resolve_object($this->c, $obj[1][1]['/Rotate']);
	    		if ($res[0] == PDF_TYPE_OBJECT)
	                return $res[1];
	            return $res;
	    	} else {
	    		if (!isset ($obj[1][1]['/Parent'])) {
	    			return false;
	    		} else {
	                $res = $this->_getPageRotation($obj[1][1]['/Parent']);
	                if ($res[0] == PDF_TYPE_OBJECT)
	                    return $res[1];
	                return $res;
	    		}
	    	}
	    }
	    
	    /**
	     * Read all /Page(es)
	     *
	     * @param object pdf_context
	     * @param array /Pages
	     * @param array the result-array
	     */
	    function read_pages (&$c, &$pages, &$result) {
	        // Get the kids dictionary
	    	$kids = $this->pdf_resolve_object ($c, $pages[1][1]['/Kids']);

	        if (!is_array($kids))
	            $this->fpdi->Error("Cannot find /Kids in current /Page-Dictionary");
	        foreach ($kids[1] as $v) {
	    		$pg = $this->pdf_resolve_object ($c, $v);
	            if ($pg[1][1]['/Type'][1] === '/Pages') {
	                // If one of the kids is an embedded
	    			// /Pages array, resolve it as well.
	                $this->read_pages ($c, $pg, $result);
	    		} else {
	    			$result[] = $pg;
	    		}
	    	}
	    }

	    
	    
	    /**
	     * Get PDF-Version
	     *
	     * And reset the PDF Version used in FPDI if needed
	     */
	    function getPDFVersion() {
	        parent::getPDFVersion();
	    	
	        if (isset($this->fpdi->importVersion) && $this->pdfVersion > $this->fpdi->importVersion) {
	            $this->fpdi->importVersion = $this->pdfVersion;
	        }
	    }
	    
	}


	///////////////////////////////////////////////////////
	////				FPDI START 		  			   ////
	///////////////////////////////////////////////////////

	//
	//  FPDI - Version 1.2
	//
	//    Copyright 2004-2007 Setasign - Jan Slabon
	//
	//  Licensed under the Apache License, Version 2.0 (the "License");
	//  you may not use this file except in compliance with the License.
	//  You may obtain a copy of the License at
	//
	//      http://www.apache.org/licenses/LICENSE-2.0
	//
	//  Unless required by applicable law or agreed to in writing, software
	//  distributed under the License is distributed on an "AS IS" BASIS,
	//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	//  See the License for the specific language governing permissions and
	//  limitations under the License.
	//

	
	define('FPDI_VERSION','1.2');

	ini_set('auto_detect_line_endings',1); // Strongly required!

	//require_once("fpdf_tpl.php");
	//require('fpdi_pdf_parser.php');
	//require_once("fpdi_pdf_parser.php");


	class FPDI extends FPDF_TPL {
	    /**
	     * Actual filename
	     * @var string
	     */
	    var $current_filename;

	    /**
	     * Parser-Objects
	     * @var array
	     */
	    var $parsers;
	    
	    /**
	     * Current parser
	     * @var object
	     */
	    var $current_parser;
	    
	    /**
	     * Highest version of imported PDF
	     * @var double
	     */
	    var $importVersion = 1.3;

	    /**
	     * object stack
	     * @var array
	     */
	    var $_obj_stack;
	    
	    /**
	     * done object stack
	     * @var array
	     */
	    var $_don_obj_stack;

	    /**
	     * Current Object Id.
	     * @var integer
	     */
	    var $_current_obj_id;
	    
	    /**
	     * The name of the last imported page box
	     * @var string
	     */
	    var $lastUsedPageBox;
	    
	    /**
	     * Constructor
	     * See FPDF-Manual
	     */
	    function FPDI($orientation='P',$unit='mm',$format='A4') {
	        parent::FPDF_TPL($orientation,$unit,$format);
	    }
	    
	    /**
	     * Set a source-file
	     *
	     * @param string $filename a valid filename
	     * @return int number of available pages
	     */
	    function setSourceFile($filename) {
	        $this->current_filename = $filename;
	        $fn =& $this->current_filename;

	        if (!isset($this->parsers[$fn]))
	            $this->parsers[$fn] = new fpdi_pdf_parser($fn,$this);
	        $this->current_parser = $this->parsers[$fn];
	        
	        return $this->parsers[$fn]->getPageCount();
	    }
	    
	    /**
	     * Import a page
	     *
	     * @param int $pageno pagenumber
	     * @return int Index of imported page - to use with fpdf_tpl::useTemplate()
	     */
	    function importPage($pageno, $boxName='/CropBox') {
	        if ($this->_intpl) {
	            return $this->error("Please import the desired pages before creating a new template.");
	        }
	        
	        $fn =& $this->current_filename;
	        
	        $parser =& $this->parsers[$fn];
	        $parser->setPageno($pageno);

	        $this->tpl++;
	        $this->tpls[$this->tpl] = array();
	        $tpl =& $this->tpls[$this->tpl];
	        $tpl['parser'] =& $parser;
	        $tpl['resources'] = $parser->getPageResources();
	        $tpl['buffer'] = $parser->getContent();
	        
	        if (!in_array($boxName, $parser->availableBoxes))
	            return $this->Error(sprintf("Unknown box: %s", $boxName));
	        $pageboxes = $parser->getPageBoxes($pageno);
	        
	        /**
	         * MediaBox
	         * CropBox: Default -> MediaBox
	         * BleedBox: Default -> CropBox
	         * TrimBox: Default -> CropBox
	         * ArtBox: Default -> CropBox
	         */
	        if (!isset($pageboxes[$boxName]) && ($boxName == "/BleedBox" || $boxName == "/TrimBox" || $boxName == "/ArtBox"))
	            $boxName = "/CropBox";
	        if (!isset($pageboxes[$boxName]) && $boxName == "/CropBox")
	            $boxName = "/MediaBox";
	        
	        if (!isset($pageboxes[$boxName]))
	            return false;
	        $this->lastUsedPageBox = $boxName;
	        
	        $box = $pageboxes[$boxName];
	        $tpl['box'] = $box;
	        
	        // To build an array that can be used by PDF_TPL::useTemplate()
	        $this->tpls[$this->tpl] = array_merge($this->tpls[$this->tpl],$box);
	        // An imported page will start at 0,0 everytime. Translation will be set in _putformxobjects()
	        $tpl['x'] = 0;
	        $tpl['y'] = 0;
	        
	        $page =& $parser->pages[$parser->pageno];
	        
	        // fix for rotated pages
	        $rotation = $parser->getPageRotation($pageno);
	        if (isset($rotation[1]) && ($angle = $rotation[1] % 360) != 0) {
	            $steps = $angle / 90;
	                
	            $_w = $tpl['w'];
	            $_h = $tpl['h'];
	            $tpl['w'] = $steps % 2 == 0 ? $_w : $_h;
	            $tpl['h'] = $steps % 2 == 0 ? $_h : $_w;
	            
	            if ($steps % 2 != 0) {
	                $x = $y = ($steps == 1 || $steps == -3) ? $tpl['h'] : $tpl['w'];
	            } else {
	                $x = $tpl['w'];
	                $y = $tpl['h'];
	            }
	            
	            $cx=($x/2+$tpl['box']['x'])*$this->k;
	            $cy=($y/2+$tpl['box']['y'])*$this->k;
	            
	            $angle*=-1; 
	            
	            $angle*=M_PI/180;
	            $c=cos($angle);
	            $s=sin($angle);
	            
	            $tpl['buffer'] = sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm %s Q',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy, $tpl['buffer']);
	        }
	        
	        return $this->tpl;
	    }
	    
	    function getLastUsedPageBox() {
	        return $this->lastUsedPageBox;
	    }
	    
	    function useTemplate($tplidx, $_x=null, $_y=null, $_w=0, $_h=0) {
	        $this->_out('q 0 J 1 w 0 j 0 G'); // reset standard values
	        $s = parent::useTemplate($tplidx, $_x, $_y, $_w, $_h);
	        $this->_out('Q');
	        return $s;
	    }
	    
	    /**
	     * Private method, that rebuilds all needed objects of source files
	     */
	    function _putimportedobjects() {
	        if (is_array($this->parsers) && count($this->parsers) > 0) {
	            foreach($this->parsers AS $filename => $p) {
	                $this->current_parser =& $this->parsers[$filename];
	                if (is_array($this->_obj_stack[$filename])) {
	                    while($n = key($this->_obj_stack[$filename])) {
	                        $nObj = $this->current_parser->pdf_resolve_object($this->current_parser->c,$this->_obj_stack[$filename][$n][1]);
							
	                        $this->_newobj($this->_obj_stack[$filename][$n][0]);
	                        
	                        if ($nObj[0] == PDF_TYPE_STREAM) {
								$this->pdf_write_value ($nObj);
	                        } else {
	                            $this->pdf_write_value ($nObj[1]);
	                        }
	                        
	                        $this->_out('endobj');
	                        $this->_obj_stack[$filename][$n] = null; // free memory
	                        unset($this->_obj_stack[$filename][$n]);
	                        reset($this->_obj_stack[$filename]);
	                    }
	                }
	            }
	        }
	    }
	    
	    /**
	     * Sets the PDF Version to the highest of imported documents
	     */
	    function setVersion() {
	        $this->PDFVersion = max($this->importVersion, $this->PDFVersion);
	    }
	    
	    /**
	     * Put resources
	     */
	    function _putresources() {
	        $this->_putfonts();
	    	$this->_putimages();
	    	$this->_putformxobjects();
	        $this->_putimportedobjects();
	        //Resource dictionary
	    	$this->offsets[2]=strlen($this->buffer);
	    	$this->_out('2 0 obj');
	    	$this->_out('<<');
	    	$this->_putresourcedict();
	    	$this->_out('>>');
	    	$this->_out('endobj');
	    }
	    
	    /**
	     * Private Method that writes the form xobjects
	     */
	    function _putformxobjects() {
	        $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		    reset($this->tpls);
	        foreach($this->tpls AS $tplidx => $tpl) {
	            $p=($this->compress) ? gzcompress($tpl['buffer']) : $tpl['buffer'];
	    		$this->_newobj();
	    		$this->tpls[$tplidx]['n'] = $this->n;
	    		$this->_out('<<'.$filter.'/Type /XObject');
	            $this->_out('/Subtype /Form');
	            $this->_out('/FormType 1');
	            
	            $this->_out(sprintf('/BBox [%.2f %.2f %.2f %.2f]',
	                ($tpl['x'] + (isset($tpl['box']['x'])?$tpl['box']['x']:0))*$this->k,
	                ($tpl['h'] + (isset($tpl['box']['y'])?$tpl['box']['y']:0) - $tpl['y'])*$this->k,
	                ($tpl['w'] + (isset($tpl['box']['x'])?$tpl['box']['x']:0))*$this->k,
	                ($tpl['h'] + (isset($tpl['box']['y'])?$tpl['box']['y']:0) - $tpl['y']-$tpl['h'])*$this->k)
	            );
	            
	            if (isset($tpl['box']))
	                $this->_out(sprintf('/Matrix [1 0 0 1 %.5f %.5f]',-$tpl['box']['x']*$this->k, -$tpl['box']['y']*$this->k));
	            
	            $this->_out('/Resources ');

	            if (isset($tpl['resources'])) {
	                $this->current_parser =& $tpl['parser'];
	                $this->pdf_write_value($tpl['resources']);
	            } else {
	                $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
	            	if (isset($this->_res['tpl'][$tplidx]['fonts']) && count($this->_res['tpl'][$tplidx]['fonts'])) {
	                	$this->_out('/Font <<');
	                    foreach($this->_res['tpl'][$tplidx]['fonts'] as $font)
	                		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
	                	$this->_out('>>');
	                }
	            	if(isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images']) || 
	            	   isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls']))
	            	{
	                    $this->_out('/XObject <<');
	                    if (isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images'])) {
	                        foreach($this->_res['tpl'][$tplidx]['images'] as $image)
	                  			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
	                    }
	                    if (isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls'])) {
	                        foreach($this->_res['tpl'][$tplidx]['tpls'] as $i => $tpl)
	                            $this->_out($this->tplprefix.$i.' '.$tpl['n'].' 0 R');
	                    }
	                    $this->_out('>>');
	            	}
	            	$this->_out('>>');
	            }

	        	$this->_out('/Length '.strlen($p).' >>');
	    		$this->_putstream($p);
	    		$this->_out('endobj');
	        }
	    }

	    /**
	     * Rewritten to handle existing own defined objects
	     */
	    function _newobj($obj_id=false,$onlynewobj=false) {
	        if (!$obj_id) {
	            $obj_id = ++$this->n;
	        }

	    	//Begin a new object
	        if (!$onlynewobj) {
	            $this->offsets[$obj_id] = strlen($this->buffer);
	            $this->_out($obj_id.' 0 obj');
	            $this->_current_obj_id = $obj_id; // for later use with encryption
	        }
	        
	    }

	    /**
	     * Writes a value
	     * Needed to rebuild the source document
	     *
	     * @param mixed $value A PDF-Value. Structure of values see cases in this method
	     */
	    function pdf_write_value(&$value)
	    {

	        switch ($value[0]) {

	    		case PDF_TYPE_NUMERIC :
	    		case PDF_TYPE_TOKEN :
	                // A numeric value or a token.
	    			// Simply output them
	                $this->_out($value[1]." ", false);
	    			break;

	    		case PDF_TYPE_ARRAY :

	    			// An array. Output the proper
	    			// structure and move on.

	    			$this->_out("[",false);
	                for ($i = 0; $i < count($value[1]); $i++) {
	    				$this->pdf_write_value($value[1][$i]);
	    			}

	    			$this->_out("]");
	    			break;

	    		case PDF_TYPE_DICTIONARY :

	    			// A dictionary.
	    			$this->_out("<<",false);

	    			reset ($value[1]);

	    			while (list($k, $v) = each($value[1])) {
	    				$this->_out($k . " ",false);
	    				$this->pdf_write_value($v);
	    			}

	    			$this->_out(">>");
	    			break;

	    		case PDF_TYPE_OBJREF :

	    			// An indirect object reference
	    			// Fill the object stack if needed
	    			$cpfn =& $this->current_parser->filename;
	    			if (!isset($this->_don_obj_stack[$cpfn][$value[1]])) {
	                    $this->_newobj(false,true);
	                    $this->_obj_stack[$cpfn][$value[1]] = array($this->n, $value);
	                    $this->_don_obj_stack[$cpfn][$value[1]] = array($this->n, $value);
	                }
	                $objid = $this->_don_obj_stack[$cpfn][$value[1]][0];

	    			$this->_out("{$objid} 0 R"); //{$value[2]}
	    			break;

	    		case PDF_TYPE_STRING :

	    			// A string.
	                $this->_out('('.$value[1].')');

	    			break;

	    		case PDF_TYPE_STREAM :

	    			// A stream. First, output the
	    			// stream dictionary, then the
	    			// stream data itself.
	                $this->pdf_write_value($value[1]);
	    			$this->_out("stream");
	    			$this->_out($value[2][1]);
	    			$this->_out("endstream");
	    			break;
	            case PDF_TYPE_HEX :
	            
	                $this->_out("<".$value[1].">");
	                break;

	    		case PDF_TYPE_NULL :
	                // The null object.

	    			$this->_out("null");
	    			break;
	    	}
	    }
	    
	    
	    /**
	     * Private Method
	     */
	    function _out($s,$ln=true) {
		   //Add a line to the document
		   if ($this->state==2) {
	           if (!$this->_intpl)
		           $this->pages[$this->page] .= $s.($ln == true ? "\n" : '');
	           else
	               $this->tpls[$this->tpl]['buffer'] .= $s.($ln == true ? "\n" : '');
	       } else {
			   $this->buffer.=$s.($ln == true ? "\n" : '');
	       }
	    }

	    /**
	     * rewritten to close opened parsers
	     *
	     */
	    function _enddoc() {
	        parent::_enddoc();
	        $this->_closeParsers();
	    }
	    
	    /**
	     * close all files opened by parsers
	     */
	    function _closeParsers() {
	        if ($this->state > 2 && count($this->parsers) > 0) {
	          	foreach ($this->parsers as $k => $_){
	            	$this->parsers[$k]->closeFile();
	            	$this->parsers[$k] = null;
	            	unset($this->parsers[$k]);
	            }
	            return true;
	        }
	        return false;
	    }

	}

	// for PHP5
	if (!class_exists('fpdi')) {
	    class fpdi extends FPDI {}
	}


	///////////////////////////////////////////////////////
	////			FPDI_PROTECTION START  			   ////
	///////////////////////////////////////////////////////
	
	class FPDI_Protection extends FPDI {
	
		var $encrypted;          //whether document is protected
	    var $Uvalue;             //U entry in pdf document
	    var $Ovalue;             //O entry in pdf document
	    var $Pvalue;             //P entry in pdf document
	    var $enc_obj_id;         //encryption object id
	    var $last_rc4_key;       //last RC4 key encrypted (cached for optimisation)
	    var $last_rc4_key_c;     //last RC4 computed key
	    var $padding = '';
	    
	    function FPDI_Protection($orientation='P',$unit='mm',$format='A5')
	    {
	        parent::FPDI($orientation,$unit,$format);
	        $this->_current_obj_id =& $this->current_obj_id; // for FPDI 1.1 compatibility
	        
	        $this->encrypted=false;
	        $this->last_rc4_key = '';
	        $this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
	                         "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
	    }

	    /**
	    * Function to set permissions as well as user and owner passwords
	    *
	    * - permissions is an array with values taken from the following list:
	    *   40bit:  copy, print, modify, annot-forms
	    *   128bit: fill-in, screenreaders, assemble, degraded-print
	    *   If a value is present it means that the permission is granted
	    * - If a user password is set, user will be prompted before document is opened
	    * - If an owner password is set, document can be opened in privilege mode with no
	    *   restriction if that password is entered
	    */
	    function SetProtection($permissions=array(),$user_pass='',$owner_pass=null)
	    {
	        $options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32 );
	        $protection = 192;
	        foreach($permissions as $permission){
	            if (!isset($options[$permission]))
	                $this->Error('Incorrect permission: '.$permission);
	            $protection += $options[$permission];
	        }
	        if ($owner_pass === null)
	            $owner_pass = uniqid(rand());
	        $this->encrypted = true;
	        $this->_generateencryptionkey($user_pass, $owner_pass, $protection);
	    }


	    function _putstream($s)
	    {
	        if ($this->encrypted) {
	            $s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
	        }
	        parent::_putstream($s);
	    }


	    function _textstring($s)
	    {
	        if ($this->encrypted) {
	            $s = $this->_RC4($this->_objectkey($this->_current_obj_id), $s);
	        }
	        return parent::_textstring($s);
	    }


	    /**
	    * Compute key depending on object number where the encrypted data is stored
	    */
	    function _objectkey($n)
	    {
	        return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)),0,10);
	    }


	    /**
	    * Escape special characters
	    */
	    function _escape($s)
	    {
	        return str_replace(
	        	array('\\',')','(',"\r"),
	        	array('\\\\','\\)','\\(','\\r'),$s);
	    }

	    function _putresources()
	    {
	        parent::_putresources();
	        if ($this->encrypted) {
	            $this->_newobj();
	            $this->enc_obj_id = $this->_current_obj_id;
	            $this->_out('<<');
	            $this->_putencryption();
	            $this->_out('>>');
	        }
	    }

	    function _putencryption()
	    {
	        $this->_out('/Filter /Standard');
	        $this->_out('/V 1');
	        $this->_out('/R 2');
	        $this->_out('/O ('.$this->_escape($this->Ovalue).')');
	        $this->_out('/U ('.$this->_escape($this->Uvalue).')');
	        $this->_out('/P '.$this->Pvalue);
	    }


	    function _puttrailer()
	    {
	        parent::_puttrailer();
	        if ($this->encrypted) {
	            $this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
	            $id = isset($this->fileidentifier) ? $this->fileidentifier : '';
	            $this->_out('/ID [<'.$id.'><'.$id.'>]');
	        }
	    }


	    /**
	    * RC4 is the standard encryption algorithm used in PDF format
	    */
	    function _RC4($key, $text)
	    {
	    	if ($this->last_rc4_key != $key) {
	            $k = str_repeat($key, 256/strlen($key)+1);
	            $rc4 = range(0,255);
	            $j = 0;
	            for ($i=0; $i<256; $i++){
	                $t = $rc4[$i];
	                $j = ($j + $t + ord($k{$i})) % 256;
	                $rc4[$i] = $rc4[$j];
	                $rc4[$j] = $t;
	            }
	            $this->last_rc4_key = $key;
	            $this->last_rc4_key_c = $rc4;
	        } else {
	            $rc4 = $this->last_rc4_key_c;
	        }

	        $len = strlen($text);
	        $a = 0;
	        $b = 0;
	        $out = '';
	        for ($i=0; $i<$len; $i++){
	            $a = ($a+1)%256;
	            $t= $rc4[$a];
	            $b = ($b+$t)%256;
	            $rc4[$a] = $rc4[$b];
	            $rc4[$b] = $t;
	            $k = $rc4[($rc4[$a]+$rc4[$b])%256];
	            $out.=chr(ord($text{$i}) ^ $k);
	        }

	        return $out;
	    }
	    

	    /**
	    * Get MD5 as binary string
	    */
	    function _md5_16($string)
	    {
	        return pack('H*',md5($string));
	    }

	    /**
	    * Compute O value
	    */
	    function _Ovalue($user_pass, $owner_pass)
	    {
	        $tmp = $this->_md5_16($owner_pass);
	        $owner_RC4_key = substr($tmp,0,5);
	        return $this->_RC4($owner_RC4_key, $user_pass);
	    }


	    /**
	    * Compute U value
	    */
	    function _Uvalue()
	    {
	        return $this->_RC4($this->encryption_key, $this->padding);
	    }


	    /**
	    * Compute encryption key
	    */
	    function _generateencryptionkey($user_pass, $owner_pass, $protection)
	    {
	        // Pad passwords
	        $user_pass = substr($user_pass.$this->padding,0,32);
	        $owner_pass = substr($owner_pass.$this->padding,0,32);
	        // Compute O value
	        $this->Ovalue = $this->_Ovalue($user_pass,$owner_pass);
	        // Compute encyption key
	        $tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
	        $this->encryption_key = substr($tmp,0,5);
	        // Compute U value
	        $this->Uvalue = $this->_Uvalue();
	        // Compute P value
	        $this->Pvalue = -(($protection^255)+1);
	    }

	    
	    function pdf_write_value(&$value) {
	    	switch ($value[0]) {
	    		case PDF_TYPE_STRING :
					if ($this->encrypted) {
	                    $value[1] = $this->_RC4($this->_objectkey($this->_current_obj_id), $value[1]);
	                 	$value[1] = $this->_escape($value[1]);
	                } 
	    			break;
	    			
				case PDF_TYPE_STREAM :
					if ($this->encrypted) {
	                    $value[2][1] = $this->_RC4($this->_objectkey($this->_current_obj_id), $value[2][1]);
	                }
	                break;
	                
	            case PDF_TYPE_HEX :
					
	            	if ($this->encrypted) {
	                	$value[1] = $this->hex2str($value[1]);
	                	$value[1] = $this->_RC4($this->_objectkey($this->_current_obj_id), $value[1]);
	                    
	                	// remake hexstring of encrypted string
	    				$value[1] = $this->str2hex($value[1]);
	                }
	                break;
	    	}	
	    	
	    	parent::pdf_write_value($value);
	    }
	    
	    
	    function hex2str($hex) {
	    	return pack("H*", str_replace(array("\r","\n"," "),"", $hex));
	    }
	    
	    function str2hex($str) {
	        return current(unpack("H*",$str));
	    }
	}

	///////////////////////////////////////////////////////
	////				PDFEST START 				   ////
	///////////////////////////////////////////////////////


	class Pdfest extends FPDI_protection
	{	
		var $pdf = '';
		var $cover = '';
		var $hp = '';
		var $email = '';
		var $pass = '';
		var $owner_pass = '';
		var $title='';

		function set_pdf_path($str){
			$this->pdf = $str;
		}

		function set_cover_path($str){
			$this->cover = $str;
		}

		function set_user_email($str){
			$this->email = $str;
		}

		function set_user_hp($str){
			$this->hp = $str;
		}

		function set_user_pass($str){
			$this->pass = $str;
		}

		function set_owner_pass($str){
			$this->owner_pass = $str;
		}

		function set_title($str){
			$this->title = $str;
		}

		function force_download(){
			
			//$ori_path = "../pdfs/";
			//$book = "cubaan";

			//$ori  = $ori_path.'ori/'.$book.'.pdf';
			//$cover_ori  = $ori_path.'covers/'.$book.'.jpg';

			if($this->pdf == ''){
				echo 'Please set pdf path as in $this->pdfest->set_pdf_path("path/to/file.pdf")';
				exit;
			}
			if($this->cover == ''){
				echo 'Please set cover path as in $this->pdfest->set_cover_path("path/to/cover.jpg")';
				exit;
			}
			if($this->email == ''){
				echo 'Please set owner email as in $this->pdfest->set_owner_email("owner@email.com")';
				exit;
			}
			if($this->hp == ''){
				echo 'Please set owner hp as in $this->pdfest->set_owner_hp("012345678")';
				exit;
			}

			$ori  = $this->pdf;
			$cover_ori  = $this->cover;

			$pat = explode('/', $cover_ori);
			$cov = $this->hp.'-'.$pat[(count($pat)-1)];
			$pat[(count($pat)-1)] = $cov;

			//var_dump($cov);
			$cover_path = implode('/', $pat);
			//var_dump($cover_path);

			$email = $this->email;
			$hp = $this->hp;

			$hash = sha1($email.$hp);
			
			//$cover_path = $ori_path.'temp/'.$hp.'.'.$book.'.jpg';

			// Path to jpeg file
			$path = $cover_ori;

			// We need to check if theres any IPTC data in the jpeg image. If there is then 
			// bail out because we cannot embed any image that already has some IPTC data!
			$image = getimagesize($path, $info);

			if(isset($info['APP13']))
			{
			    die('Error: IPTC data found in source image, cannot continue');
			}

			// Set the IPTC tags
			$iptc = array(
			    '2#120' => 'Cover For '.$this->title,
			    '2#116' => 'Copyright 2012, PTS Akademia ('.md5($email.'/'.$hp).')'
			);

			// Convert the IPTC tags into binary code
			$data = '';

			foreach($iptc as $tag => $string)
			{
			    $tag = substr($tag, 2);
			    $data .= $this->iptc_make_tag(2, $tag, $string);
			}

			// Embed the IPTC data
			$content = iptcembed($data, $path);
			
			// Write the new image data out to the file.

			$fp = fopen($cover_path, "wb");
			fwrite($fp, $content);
			fclose($fp);

			/*****************
				Siap Cover
				///buat PDF
			******************/

			$pagecount = $this->setSourceFile($ori);

			$this->SetTitle($this->title);
			//$this->SetAuthor('Izwan Wahab');
			$this->SetCreator('PTS Akademia');
			//$this->SetSubject('Subjek atau Kategori Buku Ini');
			$this->SetKeywords($hash);

			//add coverpage
			$this->addPage();
			$this->Image($cover_path,0,0,150);

			for ($n = 1; $n <= $pagecount; $n++) {

			    $tplidx = $this->ImportPage($n);
			    //
			    $this->addPage();
			    $this->useTemplate($tplidx);

			}

			
			//$this->SetTopMargin(400);

			if($this->pass != null) $this->SetProtection(array(), $this->pass, $this->owner_pass); //SET PASSWORD
			else $this->SetProtection(array(), NULL, $this->owner_pass);
			//tak kasi print
			$this->Output($this->title.'-'.$hp.'-'.date('d.m.Y.H.i.s').'.pdf', 'D');

		}

		function Footer(){ // Set FOOTER 
			
		    // Go to 1.5 cm from bottom
		    if($this->PageNo() > 1){
		    $this->SetY(-10);
		    // Select Arial italic 8
		    $this->SetFont('Helvetica','i',8);
		    // Print centered page number
		    //$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
		    $this->SetFillColor(255,255,255);
		    $this->SetTextColor(155,155,155);
		    $this->Cell(0,8, $this->email.' | '.$this->hp,0,0,'C',true);
			}
		}

		function iptc_make_tag($rec, $data, $value)
		{
		    $length = strlen($value);
		    $retval = chr(0x1C) . chr($rec) . chr($data);

		    if($length < 0x8000)
		    {
		        $retval .= chr($length >> 8) .  chr($length & 0xFF);
		    }
		    else
		    {
		        $retval .= chr(0x80) . 
		                   chr(0x04) . 
		                   chr(($length >> 24) & 0xFF) . 
		                   chr(($length >> 16) & 0xFF) . 
		                   chr(($length >> 8) & 0xFF) . 
		                   chr($length & 0xFF);
		    }

		    return $retval . $value;
		}
	}

?>