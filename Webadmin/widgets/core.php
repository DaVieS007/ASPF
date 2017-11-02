<?php
	class widget
	{
		var $temp;
		var $form;

		/** FLUSH **/
		function row()
		{
			$ret = $this->temp;
			$this->temp = "";
			return "<div class='row'>".$ret."</div>";
		}
		/** FLUSH **/

		/** GET **/
		function get()
		{
			$ret = $this->temp;
			$this->temp = "";
			return $ret;
		}
		/** GET **/

		/** ADD **/
		function add($data)
		{
			$this->temp .= $data;
		}
		/** ADD **/

		/** COL **/
		function col($col,$data)
		{
			if($col)
			{
				return '<div class="col-lg-'.$col.'">'.$data.'</div>';
			}
			else
			{
				return $data;
			}
		}
		/** COL **/

		/** HEAD **/
		function head($col,$text)
		{
			$this->temp .=  $this->col($col,'<h1 class="page-header">'.$text.'</h1>');
		}
		/** HEAD **/

		/** LEAD **/
		function lead($col,$text)
		{
			$this->temp .= $this->col($col,'<p class="lead">'.$text.'</p>');
		}
		/** LEAD **/

		/** SIGN **/
		function sign($text)
		{
			return '<div style="font-weight: bold; position: absolute; bottom: 0; right: 25px; font-size: 12px;">'.$text.'</div>';
		}
		/** SIGN **/

		/** PRELOAD **/
		function preload($col,$title,$load_url,$finish_url,$state_url)
		{
			$hash = md5($title.$load_url.$finish_url.$state_url.time().mt_rand(0,999999999));
			$data = <<<EOF
				<div id="H_[HASH]" align="center">
					<img src="/admin_html/images/ripple.svg" style="min-width: 256px; min-height: 256px; max-width: 386px;" />
					<h2>[TITLE]</h2>
				</div>
				<script>
				$("#H_[HASH]").load("[LOAD_URL]", function() {
					self.location.href='[FINISH_URL]';
				});
				</script>

EOF;

			$data2 = <<<EOF
				<div id="H_[HASH]_LL"></div>
				<div id="H_[HASH]" align="center" style="min-width: 256px; min-height: 256px; position: relative;">
					<div id="H_[HASH]_3" style="position: absolute; top: 30px; width: 100%; font-weight: bold; line-height: 40px;"></div>
					<h2 id="H_[HASH]_2">[TITLE]</h2>
				</div>

				<div id="H_[HASH]_T" style="display: none;"></div>
				<script>
				var P_[HASH]_state = false;

				$('#H_[HASH]').circleProgress({
				  value: 0,
				  animation: false,
				  fill: {gradient: ['#666666', '#000000']}
				});


				function H_[HASH]_state()
				{
					$("#H_[HASH]_T").load("[STATE_URL]", function() {

						var tmp = $("#H_[HASH]_T").html();
						if(tmp != "")
						{
							var arr = tmp.split('|');
							var progress = parseInt(arr[0]);
							var print_progress = progress;
							progress = progress / 100;

							$('#H_[HASH]').circleProgress('value', progress);
							$("#H_[HASH]_2").html(arr[1]);
							$("#H_[HASH]_3").html(print_progress + "%");
						}

						if(P_[HASH]_state == false)
						{
							setTimeout(H_[HASH]_state, 200);
						}						
					});
				}

				setTimeout(H_[HASH]_state, 200);

				$("#H_[HASH]_LL").load("[LOAD_URL]", function() {

					setTimeout(function() {
						$('#H_[HASH]').circleProgress('value', 1);
						$("#H_[HASH]_3").html("100%");
						$("#H_[HASH]_2").html("");
					}, 1000);

					setTimeout(function() {
						self.location.href='[FINISH_URL]';
					}, 2000);

				});

				</script>

EOF;


			if($state_url)
			{
				$data = $data2;
			}

			$data = str_replace("[TITLE]",$title,$data);
			$data = str_replace("[LOAD_URL]",$load_url,$data);
			$data = str_replace("[FINISH_URL]",$finish_url,$data);
			$data = str_replace("[STATE_URL]",$state_url,$data);
			$data = str_replace("[HASH]",$hash,$data);
			$this->temp .=  $this->col($col,$data);
		}
		/** PRELOAD **/


		/** INFOBAR **/
		function infobar($col,$type,$text)
		{
			$this->temp .=  $this->col($col,'<div class="alert alert-'.$type.'">'.$text.'</div>');
		}
		/** INFOBAR **/

		/** FORM_NOTE **/
		function form_note($type,$text)
		{
			$this->form .=  '<div class="alert alert-'.$type.'">'.$text.'</div>';
		}
		/** FORM_NOTE **/

		/** CODE **/
		function code($col,$text)
		{
			$text = str_replace("<","&lt;",$text);
			$text = str_replace(">","&gt;",$text);
			$this->temp .= $this->col($col,'<pre class="">'.$text.'</pre>');
		}
		/** CODE **/

		/** CODE **/
		function code_highlight($col,$text)
		{
			$text = str_replace("<","&lt;",$text);
			$text = str_replace(">","&gt;",$text);
			$this->temp .= $this->col($col,'<pre class="label-warning">'.$text.'</pre>');
		}
		/** CODE **/
		
		/** RCODE **/
		function rcode($col,$text)
		{
			$text = str_replace("<","&lt;",$text);
			$text = str_replace(">","&gt;",$text);
			return $this->col($col,'<pre>'.$text.'</pre>');
		}
		/** RCODE **/

		/** RCODE_FIXED **/
		function rcode_fixed($col,$text)
		{
			$text = str_replace("<","&lt;",$text);
			$text = str_replace(">","&gt;",$text);
			return $this->col($col,'<div class="well"><pre style="min-height: 300px; max-height: 300px; overflow: hidden;">'.$text.'</pre></div>');
		}
		/** RCODE_FIXED **/


		/** TEXT **/
		function text($col,$text)
		{
			$this->temp .= $this->col($col,'<div class="well">'.$text.'</div>');
		}
		/** TEXT **/

		/** PROGRESS **/
		function progress($type,$percent)
		{
			$data = '
			<div class="progress" style="margin-top: 3px; max-height: 2px; height: 2px;">
			    <div class="progress-bar progress-bar-'.$type.'" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%"><span class="sr-only">'.$percent.'%</span>
			    </div>
			</div>';
			return $data;
		}
		/** PROGRESS **/

		/** PANEL **/
		function panel($type,$icon,$head,$lead,$action,$link)
		{
			$data = '
			<div class="" onclick="self.location.href=\''.$link.'\'" style="margin-right: 10px; min-width: 200px; float: left;">
		        <div class="panel panel-'.$type.'">
		            <div class="panel-heading">
		                <div class="row">
		                    <div class="col-xs-3">
		                        <i class="fa fa-'.$icon.' fa-5x"></i>
		                    </div>
		                    <div class="col-xs-9 text-right">
		                        <div class="huge">'.$head.'</div>
		                        <div>'.$lead.'</div>
		                    </div>
		                </div>
		            </div>
		            <a href="'.$link.'">
		                <div class="panel-footer">
		                    <span class="pull-left">'.$action.'</span>
		                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
		                    <div class="clearfix"></div>
		                </div>
		            </a>
		        </div>
		    </div>';

		    $this->temp .= $data;
		}
		/** PANEL **/

		/** CPANEL **/
		function cpanel($type,$icon,$head,$lead,$action)
		{
			$data = '
			<div class="col-lg-6">
				<div class="" style="margin-top: 20px; width: 600px;">
			        <div class="panel panel-'.$type.'">
			            <div class="panel-heading">
			                <div class="row">
			                    <div class="col-xs-3">
			                        <i class="fa fa-'.$icon.' fa-5x"></i>
			                    </div>
			                    <div class="col-xs-9 text-right">
			                        <div class="huge">'.$head.'</div>
			                        <div>'.$lead.'</div>
			                    </div>
			                </div>
			            </div>
		                <div class="panel-footer">
		                    <span class="pull-left">'.$action.'</span>
		                    <div class="clearfix"></div>
		                </div>
			        </div>
			    </div>
			</div>';

		    $this->temp .= $data;
		}
		/** CPANEL **/

		/** TEXTAREA **/
		function textarea($id,$type,$icon,$text,$lead,$action,$link)
		{
			$data = '
			<form id="'.$id.'" action="'.$link.'" method = "POST">
				<div class="col-lg-12 col-md-8">
			        <div class="panel panel-'.$type.'">
			            <textarea name="text" style="width: 100%; height: 200px;" name="text">'.$text.'</textarea>
			            <a style="cursor: pointer;" onclick="$(\'#'.$id.'\').submit();">
			                <div class="panel-footer" style="height: 60px; line-height: 40px; font-size: 22px;">
			                    <span class="pull-left">'.$action.'</span>
			                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
			                    <div class="clearfix"></div>
			                </div>
			            </a>
			        </div>
			    </div>
			</form>
			';

		    $this->temp .= $data;
		}
		/** TEXTAREA **/

		/** DPANEL **/
		function dpanel($type,$icon,$head,$lead,$action,$id,$link)
		{
			$data = '
			<a href="'.$link.'">
			<div class="" style="margin-left: 10px; margin-top: 10px; min-width: 200px; float: left;">
		        <div class="panel panel-'.$type.'">
		            <div class="panel-heading">
		                <div class="row">
		                    <div class="col-xs-3">
		                        <i class="fa fa-'.$icon.' fa-5x"></i>
		                    </div>
		                    <div class="col-xs-9 text-right">
		                        <div class="huge">'.$head.'</div>
		                        <div>'.$lead.'</div>
		                    </div>
		                </div>
		            </div>
	                <div class="panel-footer">
	                    <span class="pull-left">'.$action.'</span>
	                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
	                    <div class="clearfix"></div>
	                </div>
		        </div>
		    </div>
		    </a>
		    ';

		    $this->temp .= $data;
		}
		/** DPANEL **/


		/** APANEL **/
		function apanel($type,$icon,$head,$lead,$action,$id,$link)
		{
			$data = '
			<a href="#" onclick="if(confirm(\''.L("DELETE_DESC").'\') == true) { $(\'#'.$id.'\').load(\''.$link.'\'); }">
			<div class="" style="margin-left: 10px; margin-top: 10px; min-width: 200px; float: left;">
		        <div class="panel panel-'.$type.'">
		            <div class="panel-heading">
		                <div class="row">
		                    <div class="col-xs-3">
		                        <i class="fa fa-'.$icon.' fa-5x"></i>
		                    </div>
		                    <div class="col-xs-9 text-right">
		                        <div class="huge">'.$head.'</div>
		                        <div>'.$lead.'</div>
		                    </div>
		                </div>
		            </div>
	                <div class="panel-footer">
	                    <span class="pull-left">'.$action.'</span>
	                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
	                    <div class="clearfix"></div>
	                </div>
		        </div>
		    </div>
		    </a>
		    ';

		    $this->temp .= $data;
		}
		/** APANEL **/

		/** RTABLE **/
		function rtable($col,$title,$th,$td,$id="",$order="",$csv_export = false)
		{
			global $url;
			$csv = isset($_GET["csv_export"]);
			if($csv)
			{
				$out = "";
				$tmp = "";
				while(list($k,$v) = each($th))
			    {
		    		if($tmp)
		    		{
		    			$tmp .= ";";
		    		}
		    		$tmp .= "\"".str_replace("\"","\\\"",$v)."\"";
			    }
			    $out .= $tmp."\n";

			    $tmp = "";
			    while(list($k,$arr) = each($td))
			    {
			    	$tmp = "";
				    while(list($k2,$v) = each($arr))
				    {
			    		if($tmp)
			    		{
			    			$tmp .= ";";
			    		}
			    		$v = preg_replace("/<\s* input [^>]+ >/xi", '   ', $v);
			    		$v = preg_replace("/<\s* button [^>]+ >/xi", '   ', $v);
			    		$v = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $v);
			    		$v = strip_tags($v);
			    		$v = str_replace("&nbsp;","   ",$v);
			    		$v = str_replace("\r"," ",$v);
			    		$v = str_replace("\n"," ",$v);
			    		$v = str_replace("\t"," ",$v);
			    		$v = explode("   ",$v)[0];
			    		$v = trim($v);
			    		$tmp .= "\"".str_replace("\"","\\\"",$v)."\"";
			    	}
			    	$out .= $tmp."\n";
			    }

			    if(!$title)
			    {
			    	$title = implode("_",$url->get());
			    }

				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename="'.$title.'.csv"');

			    echo($out);
			    die();
			}
			else
			{
				$tr = '<tr>[BODY]</tr>';	
				$main = '
			        <h2>[TITLE]</h2>
			        <!--<div class="table-responsive">-->
			        	[CSV_EXPORT]
			            <table id="[ID]" class="table table-bordered table-hover table-striped">
			   					[HEAD]
			                <tbody>
			                	[BODY]
			                </tbody>
			            </table>
			        <!--</div>-->';

			        if($csv_export)
			        {
			        	$main = str_replace("[CSV_EXPORT]",$this->button("success",L("CSV_EXPORT"),"?csv_export")."<br /><br />",$main);
			        }
			        else
			        {
			        	$main = str_replace("[CSV_EXPORT]","",$main);
			        }

			    $head = "";
			    $empty = true;
			    while(list($k,$v) = each($th))
			    {
			    	if($v)
			    	{
			    		$empty = false;
			    	}
			    	$head .= "<th style='text-align: center;'>".$v."</th>";
			    }

			    if($empty)
			    {
			    	$head = "";
			    }

				$body = "";
				if(!is_array($td))
				{
					$td = array();
				}

			    while(list($k,$arr) = each($td))
			    {
			    	$temp = "";
				    while(list($k2,$v) = each($arr))
				    {
			    		$temp .= "<td style='text-align: center;'>".$v."</td>";
			    	}
			    	$body .= str_replace("[BODY]",$temp,$tr);
			    }

			    if($head)
			    {
				    $head = "<thead>".str_replace("[BODY]",$head,$tr)."</thead>";
			    }
			    else
			    {
			    	$head = "";
			    }

			    $main = str_replace("[HEAD]",$head,$main);
			    $main = str_replace("[BODY]",$body,$main);
			    $main = str_replace("[ID]",$id,$main);
			    $main = str_replace("[TITLE]",$title,$main);

			    if(!$order)
			    {
			    	$order[0] = count($th) - 1;
			    	$order[1] = "asc";
			    }
			    else
			    {
			    	$order = explode(":",$order);
			    }

			    if($id)
			    {
			    	$main .= '
			    	<script>
			    	$(document).ready(function() {
	    				$(\'#'.$id.'\').DataTable({
	     			   		"order": [[ '.$order[0].', "'.$order[1].'" ]],
	     			   		"stateSave": true,
	     			   		"language": {
							"sEmptyTable":     "{!LANG:DT_EMPTY}",
							"sInfo":           "{!LANG:DT_SINFO}",
							"sInfoEmpty":      "{!LANG:DT_SINFO_EMPTY}",
							"sInfoFiltered":   "({!LANG:DT_FILTERED})",
							"sInfoPostFix":    "",
							"sInfoThousands":  ",",
							"sLengthMenu":     "{!LANG:DT_ENTRIES}",
							"sLoadingRecords": "{!LANG:LOADING}",
							"sProcessing":     "{!LANG:LOADING}",
							"sSearch":         "{!LANG:SEARCH}:",
							"sZeroRecords":    "{!LANG:DT_NO_RECORDS}",
							"oPaginate": {
								"sFirst":    "{!LANG:FIRST}",
								"sLast":     "{!LANG:LAST}",
								"sNext":     "{!LANG:NEXT}",
								"sPrevious": "{!LANG:PREVIOUS}"
							},
							"oAria": {
								"sSortAscending":  ": activate to sort column ascending",
								"sSortDescending": ": activate to sort column descending"
							}				        
						}
	    				});
					} );
					</script>
					';
			    }

			    return $this->col($col,$main);
			}
		}
		/** RTABLE **/

		/** TABLE **/
		function table($col,$title,$th,$td,$id="",$order="",$csv_export = false)
		{		    
			$this->temp .= $this->rtable($col,$title,$th,$td,$id,$order,$csv_export);
		}
		/** TABLE **/


		/** HIDDEN **/
		function hidden($data)
		{
			return "<hidden class='hidden'>".$data."</hidden>";
		}
		/** HIDDEN **/

		/** BUTTON **/
		function button($type,$text,$link,$confirm=NULL)
		{
			if($confirm)
			{
				return '<a href="'.$link.'" style="margin-left: 20px;" onclick="return confirm(\''.$confirm.'\');"><button type="button" class="btn btn-sm btn-'.$type.'">'.$text.'</button></a>';
			}
			else
			{
				return '<a href="'.$link.'" style="margin-left: 20px;"><button type="button" class="btn btn-sm btn-'.$type.'">'.$text.'</button></a>';
			}
		}
		/** BUTTON **/

		/** TOGGLE_BUTTON **/
		function toggle_button($type,$text,$link,$checked=false)
		{
			$toggled = '<button type="button" style="margin-left: 20px;" class="btn btn-sm btn-'.$type.'"><div style="margin-right: 10px;" class="fa fa-check-square-o"></div>'.$text.'</button>';
			$non_toggled = '<button type="button" style="margin-left: 20px;" class="btn btn-sm btn-'.$type.'"><div style="margin-right: 10px;" class="fa fa-square-o"></div>'.$text.'</button>';

			if($checked)
			{
				$button = $toggled;
			}
			else
			{
				$button = $non_toggled;
			}

			$hash = "H_".md5($text.$link.$checked);
			$data = '
				<span onclick="func_'.$hash.'()" id="'.$hash.'">
				'.$button.'
				</span>
				<span id="D'.$hash.'" style="display: none;"></span>

				<script>
				function func_'.$hash.'()
				{
					var state = "'.$checked.'";
					var elm = $("#'.$hash.'").find("div");

					if($(elm).hasClass("fa-check-square-o"))
					{
						$("#D'.$hash.'").load("'.$link.'&check=0", function()
						{
							$(elm).removeClass();
							$(elm).addClass("fa fa-square-o");
							$("D'.$hash.'").html("");
						});
					}
					else
					{
						$("#D'.$hash.'").load("'.$link.'&check=1", function()
						{
							$(elm).removeClass();
							$(elm).addClass("fa fa-check-square-o");
							$("D'.$hash.'").html("");
						});
					}

				}
				</script>
			';

			return $data;
		}
		/** TOGGLE_BUTTON **/

		/** TOGGLE_BUTTON **/
		function check_button($type,$text,$link,$checked=false)
		{
			$toggled = '<button type="button" style="margin-left: 20px;" class="btn btn-sm btn-'.$type.'"><div style="margin-right: 10px;" class="fa fa-check-square-o"></div>'.$text.'</button>';
			$non_toggled = '<button type="button" style="margin-left: 20px;" class="btn btn-sm btn-'.$type.'"><div style="margin-right: 10px;" class="fa fa-square-o"></div>'.$text.'</button>';

			if($checked)
			{
				$button = $toggled;
			}
			else
			{
				$button = $non_toggled;
			}

			$hash = "H_".md5($text.$link.$checked);
			$data = '
				<span onclick="func_'.$hash.'()" id="'.$hash.'">
				'.$button.'
				</span>
				<span id="D'.$hash.'" style="display: none;"></span>

				<script>
				function func_'.$hash.'()
				{
					var state = "'.$checked.'";
					var elm = $("#'.$hash.'").find("div");

					if($(elm).hasClass("fa-check-square-o"))
					{
						self.location.href="'.$link.'&check=0";
					}
					else
					{
						self.location.href="'.$link.'&check=1";
					}

				}
				</script>
			';

			return $data;
		}
		/** CHECK_BUTTON **/


		/** BADGE **/
		function badge($text,$type="primary")
		{
			return '<span class="label label-'.$type.'">'.$text.'</span>';
		}
		/** BADGE **/

		/** CORNER **/
		function corner($text,$type="primary")
		{
			return '<span style="margin-right: 10px;" class="label label-'.$type.'">'.$text.'</span>';
		}
		/** CORNER **/


		/** BADGE **/
		function tag($text,$type="primary")
		{
			return '<div style="text-align: center; margin: 5px;"><span class="label label-'.$type.'" style="font-size: 13px;">'.$text.'</span></div>';
		}
		/** BADGE **/

		/** LINK **/
		function link($text,$link)
		{
			return '<a href="'.$link.'">'.$text.'</a>';
		}
		/** LINK **/

		/** ALIGN **/
		function align($text,$align)
		{
			return '<div style="text-align: '.$align.';">'.$text.'</div>';
		}
		/** ALIGN **/

		/** CENTER **/
		function center($text)
		{
			return $this->align($text,"center");
		}
		/** CENTER **/

		/** LEFT **/
		function left($text)
		{
			return $this->align($text,"left");
		}
		/** LEFT **/

		/** RIGHT **/
		function right($text)
		{
			return $this->align($text,"right");
		}
		/** RIGHT **/

		/** BOLD **/
		function bold($text)
		{
			return "<b>".$text."</b>";
		}
		/** BOLD **/

		/** ITALIC **/
		function italic($text)
		{
			return "<i>".$text."</i>";
		}
		/** ITALIC **/

		/** BOX **/
		function box($col,$type,$title,$content)
		{
			$data = '
			<div class="panel panel-'.$type.'">
            <div class="panel-heading">
                <h3 class="panel-title">'.$title.'</h3>
            </div>
            <div class="panel-body">
                '.$content.'
            </div>
        	</div>';

        	$this->temp .= $this->col($col,$data);
		}
		/** BOX **/

		/** BUBBLE **/
		function bubble($col,$type,$title,$content)
		{
			$data = '
			<div class="panel panel-'.$type.'">
            <div class="panel-heading">
                <h2 style="text-align: right; height: 5px; line-height: 5px; font-size: 12px;" class="panel-title">'.$title.'</h3>
            </div>
            <div class="panel-body">
                '.$content.'
            </div>
        	</div>';

        	$this->temp .= $this->col($col,$data);
		}
		/** BUBBLE **/

		/** RBOX **/
		function rbox($col,$type,$title,$content)
		{
			$data = '
			<div class="panel panel-'.$type.'">
            <div class="panel-heading">
                <h3 class="panel-title">'.$title.'</h3>
            </div>
            <div class="panel-body">
                '.$content.'
            </div>
        	</div>';

        	return $this->col($col,$data);
		}
		/** RBOX **/

		/** FORM_SUBMIT **/
		function form_submit($type,$text)
		{
			$this->form .= '<input type="submit" value="'.$text.'" class="btn btn-'.$type.'" />';
		}
		/** FORM_SUBMIT **/

		/** DIV **/
		function div($id,$content)
		{
			$this->temp .= "<div id='".$id."'>".$content."</div>";
		}
		/** DIV **/

		/** RDIV **/
		function rdiv($id,$content)
		{
			return "<div id='".$id."'>".$content."</div>";
		}
		/** RDIV **/

		/** LOAD **/
		function load($id,$url,$icon,$content = "")
		{
			$data = '<div style="cursor: pointer; float: left; margin-left: 5px; margin-right: 5px;" onclick="$(\'#'.$id.'\').load(\''.$url.'\');" class="fa fa-'.$icon.' fa-2x">'.$content.'</div>';
			return $data;
		}
		/** LOAD **/

		/** FLOAD **/
		function fload($id,$url,$content = "")
		{
			$data = '<div class="alert alert-info" style="cursor: pointer; margin-left: 5px; margin-right: 5px;" onclick="$(\'#'.$id.'\').load(\''.$url.'\'); $(this).html(\'<div align=\\\'center\\\'><img src=\\\'/admin_html/images/pbar.gif\\\' /></div>\');">'.$content.'</div>';
			return $data;
		}
		/** FLOAD **/


		/** SEARCHBAR **/
		function searchbar($col,$name,$value)
		{
			$data = '
			<div class="form-group">
                <input id="searchbar" style="width: 80%;  float: left;" class="form-control" type="text" value="'.$value.'" /> 
                <button style="cursor: pointer; float: left;" type="button" class="btn btn-danger" onclick="javascript:self.location.href = \'?search=\' + encodeURIComponent($(\'#searchbar\').val());">'.$name.'</button>
            </div>

            <script>
			$("#searchbar").keypress(function (e) {
			  if (e.which == 13) {
			    self.location.href = \'?search=\' + encodeURIComponent($(\'#searchbar\').val());
			  }
			});            
			</script>
            ';

			$this->temp .= $this->col($col,$data);
		}
		/** SEARCHBAR **/

		/** SELF_LOCATION **/
		function self_location($url)
		{
			return "<script>self.location.href='".$url."';</script>";
		}
		/** SELF_LOCATION **/

		/** FORM_SEP **/
		function form_sep()
		{
			$this->form .= "<div style='clear: both;'><hr /><div style='clear: both;'>";
		}
		/** FORM_SEP **/

		/** INLINE_EDIT **/
		function inline_edit($name,$value)
		{
			$value = str_replace("\"","'",$value);
			$html = '<input READONLY style="background-color: transparent; border: 0px; cursor: text;" class="form-control" name="[NAME]" type="text" value="[VALUE]" />';
			$html = str_replace("[NAME]",$name,$html);
			$html = str_replace("[VALUE]",$value,$html);

			return $html;
		}
		/** INLINE_EDIT **/

		/** INLINE_TINYEDIT **/
		function inline_tinyedit($name,$value)
		{
			$value = str_replace("\"","'",$value);
			$html = '<input READONLY style="background-color: transparent; border: 0px; width: 90px; cursor: text;" class="form-control" name="[NAME]" type="text" value="[VALUE]" />';
			$html = str_replace("[NAME]",$name,$html);
			$html = str_replace("[VALUE]",$value,$html);

			return $html;
		}
		/** INLINE_TINYEDIT **/



		/** FORM_INPUT **/
		function form_input($name,$type,$title,$help,$value,$psize=NULL)
		{
			if($type == "hidden")
			{
				$hidden = "display: none;";
			}
			else
			{
				$hidden = "";
			}

			if($psize > 0)
			{
				$psize = (100 / $psize) - $psize;
				$psize = "float: left; margin-right: 5px; width: ".$psize."%";
			}

			$data = '
			<div class="form-group" style="'.$hidden." ".$psize.'">
                <label>'.$title.'</label>
                <input class="form-control" name="'.$name.'" type="'.$type.'" value="'.$value.'" />
                <p class="help-block">'.$help.'</p>
            </div>
            ';

			$this->form .= $data;
		}
		/** FORM_INPUT **/


		/** FORM_TEXT **/
		function form_text($name,$title,$help,$value)
		{
			$data = '
			<div class="form-group" style="'.$hidden.'">
				<label>'.$title.'</label>
				<textarea class="form-control" style="width: 100%; height: 80px;" name='.$name.' >'.htmlspecialchars($value).'</textarea>
                <p class="help-block">'.$help.'</p>
            </div>
            ';

			$this->form .= $data;
		}
		/** FORM_TEXT **/

		/** FORM_DATE **/
		function form_date($name,$title,$help,$value)
		{
			$id = "datepick_hash_".md5($name);
			$date = strtotime($value);
			$date = date("Y/m/d",$date);

			$data = '
			<div class="form-group" style="'.$hidden.'">
				<label>'.$title.'</label>
				<input class="form-control" name="'.$name.'" id="'.$id.'" data-toggle="'.$id.'" />
				<script>
				$(function () {
					$("#'.$id.'").datepicker({format: \'yyyy-mm-dd\'});
					$("#'.$id.'").datepicker(\'setDate\',\''.$date.'\');
					
				});
				</script>
                <p class="help-block">'.$help.'</p>
            </div>
            ';

			$this->form .= $data;
		}
		/** FORM_DATE **/

		/** DROPZONE **/
		function dropzone($col,$ID,$action)
		{
			$this->temp .= $this->col($col,'<form action="'.$action.'" class="dropzone"></form>');
		}
		/** FORM_TEXT **/
		

		/** FORM_LABEL **/
		function form_label($title,$help,$value)
		{
			$data = '
			<div class="form-group">
                <label>'.$title.'</label>
                <div class="form-control">'.$value.'</div>
                <p class="help-block">'.$help.'</p>
            </div>
            ';

			$this->form .= $data;
		}
		/** FORM_LABEL **/


		/** FORM_AUTOCOMPLETE **/
		function form_autocomplete($name,$cb,$title,$help,$value,$psize=NULL)
		{

			if($psize > 0)
			{
				$psize = (100 / $psize) - $psize;
				$psize = "float: left; margin-right: 5px; width: ".$psize."%";
			}
			
			$hash = "H".md5("ac_".$name.$cb);
			$data = '
			<div class="form-group" style="'.$hidden." ".$psize.'">
                <label>'.$title.'</label>
                <input id="'.$hash.'" class="form-control" name="'.$name.'" type="text" value="'.$value.'" />
                <p class="help-block">'.$help.'</p>
            </div>

			<script>
				var options = {
					url: "?ac='.$cb.'",

					getValue: "name",

					list: {
						match: {
							enabled: true
						}
					}
				};

				$( document ).ready(function() {
					$("#'.$hash.'").easyAutocomplete(options);
				});				

            </script>
            ';

			$this->form .= $data;
		}
		/** FORM_AUTOCOMPLETE **/



		/** CODE39 **/
		function form_code39($text,$help)
		{
			global $URL;
			global $url;
			$nurl[0] = $URL[0];
			$nurl[1] = "code39";
			$nurl[2] = $text;

			$data = '
			<div class="form-group">
				<img src="'.$url->write($nurl).'" />
                <p class="help-block">'.$help.'</p>
            </div>
            ';

			$this->form .= $data;
		}
		/** CODE39 **/

		/** ONOFF **/
		function onoff($name,$title,$checked)
		{
			if($checked)
			{
				$checked = "CHECKED='checked'";
			}
			else
			{
				$checked = "";
			}

			$hash = md5($name.$title.$checked);

			$data = '
			<table border="0">
				<tr>
					<td width="100%">
						'.$title.'
					</td>
					<td>
						<div class="onoffswitch" style="">
						    <input type="checkbox" name="'.$name.'" class="onoffswitch-checkbox" id="'.$hash.'" '.$checked.'>
						    <label class="onoffswitch-label" for="'.$hash.'"></label>
						</div>
					</td>
				</tr>
			</table>
            ';
			return $data;
		}
		/** ONOFF **/
		
		/** FORM_CHECKBOX **/
		function form_checkbox($name,$title,$checked,$psize=NULL)
		{
			if($checked)
			{
				$checked = "CHECKED='checked'";
				$active = "active";
			}
			else
			{
				$checked = "";
				$active = "";
			}

			$hash = md5($name.$title.$checked);

			if($psize > 0)
			{
				$psize = (100 / $psize) - $psize;
				$psize = "margin-top: 25px; float: left; margin-right: 5px; width: ".$psize."%";
			}
			

			$data = '
	        <div data-toggle="buttons" style="'.$psize.'">
				<label class="button-checkbox btn btn-default btn-block '.$active.'" for="'.$hash.'">
					<input type="checkbox" autocomplete="off" name="'.$name.'" id="'.$hash.'" '.$checked.'> '.$title.'
				</label>
			</div>
            ';

			$this->form .= $data;
		}
		/** FORM_CHECKBOX **/

		/** FORM_COLOR **/
		function form_color($name,$type,$title,$help,$value)
		{
			$data = '
			<div class="form-group">
                <label>'.$title.'</label>
                <input class="form-control jscolor" name="'.$name.'" type="'.$type.'" value="'.$value.'" />
                <p class="help-block">'.$help.'</p>
            </div>
            ';

			$this->form .= $data;
		}
		/** FORM_COLOR **/

		/** FORM_SELECT **/
		function form_select($name,$title,$help,$arr,$selected,$psize=NULL)
		{

			if($psize > 0)
			{
				$psize = (100 / $psize) - $psize;
				$psize = "float: left; margin-right: 5px; width: ".$psize."%";
			}
			
			$data = '
			<div class="form-group" style="'.$psize.'">
                <label>[TITLE]</label>
                <select name="[NAME]" class="form-control">
                [OPTION]
                </select>
                <p class="help-block">[HELP]</p>
            </div>
            ';

            $option = '<option value="[VALUE]" [SEL]>[NAME]</option>';

            $data = str_replace("[TITLE]",$title,$data);
            $data = str_replace("[HELP]",$help,$data);

			
			$opts = "";
			if(!is_array($arr))
			{
				$arr = array();
			}
            while(list($k,$v) = each($arr))
            {
            	$temp = $option;
            	$temp = str_replace("[NAME]",$v,$temp);
            	$temp = str_replace("[VALUE]",$k,$temp);
            	if($k == $selected)
            	{
            		$temp = str_replace("[SEL]","SELECTED",$temp);
            	}
            	else
            	{
            		$temp = str_replace("[SEL]","",$temp);            		
            	}
            	$opts .= $temp;
            }

            $data = str_replace("[OPTION]",$opts,$data);
            $data = str_replace("[NAME]",$name,$data);

			$this->form .= $data;
		}
		/** FORM_SELECT **/

		/** ICON **/
		function icon($icon,$url)
		{
			$data = '<div style="cursor: pointer; margin-right: 5px;" onclick="self.location.href=\''.$url.'\';" class="fa fa-'.$icon.'"></div>';
			return $data;
		}
		/** ICON **/

		/** INLINE_FORM **/
		function inline_form($url,$icon)
		{
			$data = '<div style="cursor: pointer; float: left; margin-left: 5px; margin-right: 5px;" onclick="inline_form(\''.$url.'\');" class="fa fa-'.$icon.' fa-lg"></div>';
			return $data;
		}
		/** INLINE_FORM **/

		/** INLINE_BUTTON **/
		function inline_button($link,$icon,$confirm)
		{
			if($confirm)
			{
				return '<a href="'.$link.'" style="margin-left: 20px;" onclick="return confirm(\''.$confirm.'\');"><div style="cursor: pointer; color: #C9302C; float: left; margin-left: 5px; margin-right: 5px;" class="fa fa-'.$icon.' fa-lg"></div></a>';				
			}
			else
			{
				return '<a href="'.$link.'" style="margin-left: 20px;"><div style="cursor: pointer; float: left; margin-left: 5px; margin-right: 5px;" class="fa fa-'.$icon.' fa-lg"></div></a>';				
			}
		}
		/** INLINE_BUTTON **/

		/** INLINE_BUTTON_LEGACY **/
		function inline_button_legacy($link,$type,$icon,$text,$confirm)
		{
			//<button type="button" class="btn btn-sm btn-'.$type.'">'.$text.'</button></a>';
			if($confirm)
			{
				return '<a onclick="inline_form(\''.$link.'\');" class="btn btn-sm btn-'.$type.'" onclick="return confirm(\''.$confirm.'\');"><i class="fa fa-'.$icon.' fa-lg"></i> '.$text.'</a>';
			}
			else
			{
				return '<a onclick="inline_form(\''.$link.'\');" class="btn btn-sm btn-'.$type.'"><i class="fa fa-'.$icon.' fa-lg"></i> '.$text.'</a>';
			}
		}
		/** INLINE_BUTTON_LEGACY **/
		

		/** TERMINAL **/
		function terminal($url,$text)
		{
			$data = '<div class="btn btn-lg btn-default fa fa-terminal fa-2x" style="cursor: pointer; float: left; margin-left: 5px; margin-right: 5px;" onclick="terminal(\''.$url.'\');">  <span style="font-size: 16px; font-weight: bold; margin-left: 10px;">'.$text.'</span></div>';
			return $data;
		}
		/** TERMINAL **/

		/** ID_CARD **/
		function id_card($col,$ID)
		{
			global $URL;
			global $url;

			$nurl = array();
			$nurl[0] = $URL[0];
			$nurl[1] = "id-card";
			$nurl[2] = $ID;
			$_url = $url->write($nurl);

			$this->temp .= $this->col($col,"<a href='".$_url."' download><img src='".$_url."' /></a>");
		}
		/** ID_CARD **/

		/** ID_CARD **/
		function id_card2($col,$ID)
		{
			global $URL;
			global $url;

			$nurl = array();
			$nurl[0] = $URL[0];
			$nurl[1] = "id-card";
			$nurl[2] = $ID;
			$_url = $url->write($nurl);

			$this->temp .= $this->col($col,"<img src='".$_url."' />");
		}
		/** ID_CARD **/

		/** IMAGE_SELECT **/
		function image_select($col,$name1,$title1,$url1,$name2,$title2,$url2)
		{
			$data = '<table border="0" style="width: 100%;">';

			$data .= "<tr>";
			$data .= '<td align="center"><a href="'.$url1.'" align="center"><img src="/admin_html/images/'.$name1.'" /></a></td>';
			$data .= '<td align="center"><a href="'.$url2.'" align="center"><img src="/admin_html/images/'.$name2.'" /></a></td>';
			$data .= "</tr>";

			$data .= "<tr>";
			$data .= '<td align="center" width="50%"><h3 style="margin: 0px;">'.$title1.'</h3></td>';
			$data .= '<td align="center" width="50%"><h3>'.$title2.'</h3></td>';
			$data .= "</tr>";

			$data .= "</table>";
			$this->temp .= $this->col($col,$data);
		}
		/** IMAGE_SELECT **/

		/** IMAGE_SELECT_SINGLE **/
		function image_select_single($col,$name1,$title1,$url1)
		{
			$data = '<table border="0" style="width: 100%;">';

			$data .= "<tr>";
			$data .= '<td align="center"><a href="'.$url1.'" align="center"><img style="max-height: 256px; padding: 20px;" src="/admin_html/images/'.$name1.'" /></a></td>';
			$data .= "</tr>";

			$data .= "<tr>";
			$data .= '<td align="center" width="50%"><h3 style="margin: 0px;">'.$title1.'</h3></td>';
			$data .= "</tr>";


			$data .= "</table>";
			$this->temp .= $this->col($col,$data);
		}
		/** IMAGE_SELECT_SINGLE **/

		/** INLINE_FORM_BUTTON **/
		function inline_form_btn($url,$text,$type)
		{
			$data = '<div style="cursor: pointer; float: left; margin-left: 5px; margin-right: 5px;" onclick="inline_form(\''.$url.'\');"><button type="button" class="btn btn-'.$type.'">'.$text.'</button></div>';
			return $data;
		}
		/** INLINE_FORM_BUTTON **/

		/** FORM **/
		function form($col,$type,$title,$submit=NULL,$cancel=NULL,$cancel_text=NULL)
		{
			if(!$submit)
			{
				$submit = L("FORM_SUBMIT");
			}

			$this->form_submit($type,$submit);
			if($cancel && $cancel_text)
			{
				$this->form .= $this->button("info",$cancel_text,$cancel);
			}
			else if($cancel)
			{
				$this->form .= $this->button("info",L("CANCEL"),$cancel);
			}



			$form_data = $this->form;
			$this->form = "";
			$data = '
				<form role="form" method="POST" enctype="multipart/form-data">
					'.$form_data.'
				</form>';

			$this->box($col,$type,$title,$data);
		}
		/** FORM **/

	}
?>