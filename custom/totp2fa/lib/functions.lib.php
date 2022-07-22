<?php
/* Copyright (C) 2017 Sergi Rodrigues <proyectos@imasdeweb.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/*
    this function should not be necessary, but i've not understood why the dolibarr price() function doesn't render thousands separator
*/
function _render_view($viewname,Array $vars){
    global $langs, $db, $conf;
    
    // == passed vars
        if (count($vars)>0){
            foreach($vars as $__k__=>$__v__){
                ${$__k__} = $__v__;
            }
        }
        
    // == we begin a new output
        ob_start();
        include(__DIR__.'/views/'.$viewname.'.php');
        
    // == we get the current output
        $render = ob_get_clean();

        return $render;
}


function _var($arr, $title=''){
	return _var_export($arr, $title);
}

function _var_export($arr, $title=''){

	//if (!is_string($arr) && !is_array($arr)) {echo 'what is this? '.var_dump($arr);die();}
    $ii++;
    if ($ii==1){ // root DIV
        $html = !empty($title) ? '<h3>'.$title.'</h3>' : '';
        $html .= "\n<div class='_var_export' style='font-family:monospace;word-break:break-all;'>";
    }else{
        $html = "\n<div style='margin-left:100px;'>";
    }

    if (is_resource($arr)){
        $arr = 'RESOURCE OF TYPE: '.get_resource_type($arr); // -> "convert" resource to string
        $is_object = false;
    }else if (is_object($arr)){
        $is_object = true;
        $arr = get_object_vars($arr);
    }else if ($is_object==''){
        $is_object = false;
    }

	if (is_array($arr)){
            if (count($arr)==0){
                $html .= "&nbsp;";
            }else{
                foreach ($arr as $k=>$ele){
                    $html .= "\n\t<div style='float:left;'><b style='".($is_object ? 'background-color:rgba(0,0,0,0.1);padding:2px':'')."'>$k <span class='arrow' style='color:#822;'>&rarr;</span> </b></div>"
                            ."\n\t<div style='border:1px #ddd solid;font-size:10px;font-family:sans-serif;'>";
                    $html .= is_object($ele) || is_resource($ele)? _var_export(get_object_vars($ele),'',$b_htmlspecialchars,true,$ii) : _var_export($ele,'',$b_htmlspecialchars,false,$ii);
                    $html .= "</div>";
                    $html .= "\n\t<div style='float:none;clear:both;'></div>";
                }
            }
	}else if ($arr===NULL){
            $html .= "&nbsp;";

    }else if (substr($arr,0,2)=='{"' || substr($arr,0,3)=='[{"'){
            $json = f_json_decode($arr);
            $html .= is_array($json) ? htmlspecialchars($arr).'<br /><br />'._var_export($json,'',$b_htmlspecialchars,false,$ii).'<br />' : $arr;

	}else if ($arr === 'b:0;' || substr($arr,0,2)=='a:'){
            $uns = f_unserialize($arr);
            if (is_array($uns))
                $html .= htmlspecialchars($arr).'<br /><br />'._var_export($uns,'',$b_htmlspecialchars,false,$ii).'<br />';
            else
                $html .= $b_htmlspecialchars==1 ? htmlspecialchars($arr) : $arr;
        }else{
            $html .= $b_htmlspecialchars==1 ? htmlspecialchars($arr) : $arr;
        }
	$html .= "</div>";

	return $html;
	
}

