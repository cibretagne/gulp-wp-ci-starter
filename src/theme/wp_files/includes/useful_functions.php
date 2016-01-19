<?php

/**
 * Useful functions
 */

function truncateHTML($str, $len, $end = '&hellip;') {
	$tagPattern = '/(<\/?)([\w]*)(\s*[^>]*)>?|&[\w#]+;/i';  //match html tags and entities
	preg_match_all($tagPattern, $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
	$i =0;

	if (isset($matches[$i])) {
		while($matches[$i][0][1] < $len && !empty($matches[$i])){
		    $len = $len + strlen($matches[$i][0][0]);
		    if(substr($matches[$i][0][0],0,1) == '&' )
		        $len = $len-1;

		    if(!empty($matches[$i][2][0]) && !in_array($matches[$i][2][0],array('br','img','hr', 'input', 'param', 'link'))){
		        if(substr($matches[$i][3][0],-1) !='/' && substr($matches[$i][1][0],-1) !='/')
		            $openTags[] = $matches[$i][2][0];
		        elseif(end($openTags) == $matches[$i][2][0]){
		            array_pop($openTags);
		        }else{
		            $warnings[] = "html has some tags mismatched in it:  $str";
		        }
		    }

		    $i++;

		    if (!isset($matches[$i]))
		    	break;
		}
	}

	$closeTags = '';

	if (!empty($openTags)) {
	    $openTags = array_reverse($openTags);
	    foreach ($openTags as $t){
	    	if (!isset($closeTagString))
	    		$closeTagString = '';
	        $closeTagString .="</".$t . ">"; 
	    }
	}

	if (strlen($str)>$len) {
	    $truncated_html = substr($str, 0, $len);
	    $truncated_html .= $end ;
	    $truncated_html .= $closeTagString;
	}
	else
		$truncated_html = $str;

	return $truncated_html; 
}

/**
 * @param $color_code
 * @param int $percentage_adjuster
 * @return array|string
 * @author Jaspreet Chahal
 */

function adjustColorLightenDarken($color_code,$percentage_adjuster = 0) {

    $percentage_adjuster = round($percentage_adjuster/100,2);
    if(is_array($color_code)) {
        $r = $color_code["r"] - (round($color_code["r"])*$percentage_adjuster);
        $g = $color_code["g"] - (round($color_code["g"])*$percentage_adjuster);
        $b = $color_code["b"] - (round($color_code["b"])*$percentage_adjuster);
 
        return array("r"=> round(max(0,min(255,$r))),
            "g"=> round(max(0,min(255,$g))),
            "b"=> round(max(0,min(255,$b))));
    }
    else if(preg_match("/#/",$color_code)) {
        $hex = str_replace("#","",$color_code);
        $r = (strlen($hex) == 3)? hexdec(substr($hex,0,1).substr($hex,0,1)):hexdec(substr($hex,0,2));
        $g = (strlen($hex) == 3)? hexdec(substr($hex,1,1).substr($hex,1,1)):hexdec(substr($hex,2,2));
        $b = (strlen($hex) == 3)? hexdec(substr($hex,2,1).substr($hex,2,1)):hexdec(substr($hex,4,2));
        $r = round($r - ($r*$percentage_adjuster));
        $g = round($g - ($g*$percentage_adjuster));
        $b = round($b - ($b*$percentage_adjuster));
 
        return "#".str_pad(dechex( max(0,min(255,$r)) ),2,"0",STR_PAD_LEFT)
            .str_pad(dechex( max(0,min(255,$g)) ),2,"0",STR_PAD_LEFT)
            .str_pad(dechex( max(0,min(255,$b)) ),2,"0",STR_PAD_LEFT);
 
    }
    
}