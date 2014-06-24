<?php

	function my_reg_split($s_content,$s_pattern){
		
		$start = strpos ($s_content, $s_pattern);
	 
		$s_len = mb_strlen($s_content,"utf-8");
		
		$i = 1;
		$pat_addr[0] = $start;
		
		while($start != $s_len){
			
			$start = $start+20;
			
			$sub_content = substr($s_content,$start,$s_len);
			$s_pat = strpos ($sub_content, $s_pattern);
			if( $s_pat != false){
				$addr = $start+$s_pat;
				$pat_addr[$i] = $addr;
			
				$start = $addr;
				$i++;
			}
			else{
				$start = $s_len;
			}
		
		}
		
		$max = count($pat_addr);
		
		for($i=0;$i<=$max;$i++){
			if($i==0){
				//echo "From 0 To ".$pat_addr[$i]."<br/>";
				$len = 0;
				$len = $pat_addr[$i]-0;
				$o_result = substr($s_content,0,$len);
				$o_result = @ereg_replace ('</b>','', $o_result);
				$o_result = @ereg_replace ('<br>','', $o_result);
				$s_result[$i] = @ereg_replace ('<b>','', $o_result);
			}
			else if($i==$max){
				//echo "From ".$pat_addr[$i-1]." To ".$s_len."<br/>";
				$len = 0;
				$len = $s_len-$pat_addr[$i-1];
				$o_result = substr($s_content,$pat_addr[$i-1],$len);
				$o_result = @ereg_replace ('</b>','', $o_result);
				$o_result = @ereg_replace ('<br>','', $o_result);
				$s_result[$i] = @ereg_replace ('<b>','', $o_result);
			}
			else{
				//echo "From ".$pat_addr[$i-1]." To ".$pat_addr[$i]."<br/>";
				$len = 0;
				$len = $pat_addr[$i]-$pat_addr[$i-1];
				$o_result = substr($s_content,$pat_addr[$i-1],$len);
				$o_result = @ereg_replace ('</b>','', $o_result);
				$o_result = @ereg_replace ('<br>','', $o_result);
				$s_result[$i] = @ereg_replace ('<b>','', $o_result);
			}
		}
		
		return $s_result;
	}
	
	
	function big52utf8($big5str) {
        $blen = strlen($big5str);
        $utf8str = "";

        for($i=0; $i<$blen; $i++) {
            $sbit = ord(substr($big5str, $i, 1));
            //echo $sbit;
            //echo "<br>";
            if ($sbit < 129) {
                $utf8str.=substr($big5str,$i,1);
            }
            elseif ($sbit > 128 && $sbit < 255) {
                $new_word = @iconv("big5", "UTF-8", substr($big5str,$i,2));
                $utf8str.=($new_word=="")?" ":$new_word;
                $i++;
            }
        }
        return $utf8str;
    }




?>