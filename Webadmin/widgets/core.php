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

		function kiosk_login($cb_url)
		{
			$hash = "H".md5($cb);

			$data = <<<EOF

			<img id="N[HASH]" style="display: none; position: relative; margin-left: -128px; left: 50%; width: 256px;" src="/common_html/images/nfc.png" />
			<img id="R[HASH]" style="display: none; position: relative; margin-left: -128px; left: 50%; width: 256px;" src="/admin_html/images/ripple.svg" />
			<div id="T[HASH]" style="text-align: center; width: 100%; font-size: 24px; font-weight: bold;"></div>
			<input type="text" style="position: relative; border: 0px; color: transparent; outline: none; width: 400px; left: 50%; margin-left: -200px;" id="[HASH]">

			
			<script>
			var last_key = 0;

			$( document ).ready(function() {
			    $('#[HASH]').focus();
			    $('#N[HASH]').fadeIn(2000);	
			});				

		    $('#[HASH]').focusout(function() {
		    	$('#[HASH]').focus();
		    });

			$('#[HASH]').keyup(function() {
				last_key = $.now();
				$('#N[HASH]').hide();
				$('#R[HASH]').show();
				$('#T[HASH]').html("");
			});

			function check_login()
			{
				console.log("check_login()");
				var login_input = $('#[HASH]').val();
				if(login_input.length > 0)
				{
					if(last_key != 0 && last_key + 500 < $.now())
					{
						$('#[HASH]').val("");
						console.log("CHECKING: " + login_input);
						$('#T[HASH]').load('?[CB]' + "=" + login_input,function() { 
							$('#R[HASH]').hide();
							$('#N[HASH]').fadeIn(2000);							
						});
						last_key = 0;
					}

					if(last_key == 0 || last_key + 2000 < $.now())
					{
						if($('#R[HASH]').is(':visible'))
						{
							$('#R[HASH]').hide();
							$('#N[HASH]').fadeIn(2000);																				
						}
					}
				}


				setTimeout(check_login, 100);
			}

			setTimeout(check_login, 1000);

			</script>

EOF;

			$data = str_replace("[HASH]",$hash,$data);
			$data = str_replace("[CB]",$cb_url,$data);


			$this->temp .= $data;
		}

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
			$this->temp .= $this->col($col,'<div class="well"><pre>'.$text.'</pre></div>');
		}
		/** CODE **/

		/** RCODE **/
		function rcode($col,$text)
		{
			$text = str_replace("<","&lt;",$text);
			$text = str_replace(">","&gt;",$text);
			return $this->col($col,'<div class="well"><pre>'.$text.'</pre></div>');
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
			<div class="progress" style="margin: 0px; padding: 0px; max-height: 4px; height: 4px;">
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
			        <div class="table-responsive">
			        	[CSV_EXPORT]
			            <table id="[ID]"class="table table-bordered table-hover table-striped">
			   					[HEAD]
			                <tbody>
			                	[BODY]
			                </tbody>
			            </table>
			        </div>';

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
				if(count($td))
				{
					while(list($k,$arr) = each($td))
					{
						$temp = "";
						while(list($k2,$v) = each($arr))
						{
							$temp .= "<td style='text-align: center;'>".$v."</td>";
						}
						$body .= str_replace("[BODY]",$temp,$tr);
					}
	
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

		/** PDF_TABLE **/
		function pdf_table($th,$td)
		{
			$tr = '<tr>[BODY]</tr>';
			
			$main = '
		            <table cellspacing="0" cellpadding="2" border="1">
	                	[HEAD]
	                	[BODY]
		            </table>';

		    $head = "";
		    while(list($k,$v) = each($th))
		    {
		    	$head .= "<th align=\"center\">".$v."</th>";
		    }

		    $body = "";
		    while(list($k,$arr) = each($td))
		    {
		    	$temp = "";
			    while(list($k2,$v) = each($arr))
			    {
		    		$temp .= "<td align=\"center\">".$v."</td>";
		    	}
		    	$body .= str_replace("[BODY]",$temp,$tr);
		    }
		    $head = str_replace("[BODY]",$head,$tr);

		    $main = str_replace("[HEAD]",$head,$main);
		    $main = str_replace("[BODY]",$body,$main);

		    return $main;
		}
		/** PDF_TABLE **/

		/** TABLE **/
		function table($col,$title,$th,$td,$id="",$order="",$csv_export = false)
		{		    
			$this->temp .= $this->rtable($col,$title,$th,$td,$id,$order,$csv_export);
		}
		/** TABLE **/


		/** HIDDEN **/
		function hidden($data)
		{
			return "<hidden>".$data."</hidden>";
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


		/** BUTTON **/
		function kiosk_button($type,$text,$link,$confirm=NULL)
		{
			if($confirm)
			{
				return '<a href="'.$link.'" style="margin-left: 20px;" onclick="return confirm(\''.$confirm.'\');"><button style="font-size: 30px;" type="button" class="btn btn-lg btn-'.$type.'">'.$text.'</button></a>';
			}
			else
			{
				return '<a href="'.$link.'" style="margin-left: 20px;"><button style="font-size: 30px;" type="button" class="btn btn-lg btn-'.$type.'">'.$text.'</button></a>';
			}
		}
		/** BUTTON **/

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
			return '<div style="text-align: center; margin: 5px;"><span class="label label-'.$type.'" style="font-size: 14px;">'.$text.'</span></div>';
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
			$this->form .= "<hr />";
		}
		/** FORM_SEP **/

		/** FORM_INPUT **/
		function form_input($name,$type,$title,$help,$value)
		{
			if($type == "hidden")
			{
				$hidden = "display: none;";
			}
			else
			{
				$hidden = "";
			}

			$data = '
			<div class="form-group" style="'.$hidden.'">
                <label>'.$title.'</label>
                <input class="form-control" name="'.$name.'" type="'.$type.'" value="'.$value.'" />
                <p class="help-block">'.$help.'</p>
            </div>
            ';

			$this->form .= $data;
		}
		/** FORM_INPUT **/

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
		function form_autocomplete($name,$cb,$title,$help,$value)
		{
			$hash = "H".md5("ac_".$name.$cb);
			$data = '
			<div class="form-group" style="'.$hidden.'">
                <label>'.$title.'</label>
                <input id="'.$hash.'" class="form-control" name="'.$name.'" type="'.$type.'" value="'.$value.'" />
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

				$("#'.$hash.'").easyAutocomplete(options);

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

		/** FORM_CHECKBOX **/
		function form_checkbox($name,$title,$checked)
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
		function form_select($name,$title,$help,$arr,$selected)
		{
			$data = '
			<div class="form-group">
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
		/** FORM_INPUT **/

		/** CHART **/
		function chart($col,$url,$shadow,$ID="")
		{
			$data = $this->col($col,'<img style="cursor: pointer; margin-bottom: 30px;" class="chart_autosize chart_shadow_'.strtolower($shadow).'" onclick="chart_preview(\''.$url.'\',\''.$ID.'\');" data-src="'.$url.'"></img>');
			$this->temp .= $data;
		}
		/** CHART **/

		/** CHART_FULL **/
		function chart_full($col,$url)
		{
$data = <<<EOF
			<div style="position: relative;">
				<img style="position: absolute; z-index: 0; left: 40%; margin-left: -64px; top: 80px;" id="chart_image_full_preload" src="/admin_html/images/ripple.svg" />
				<img id="chart_image_full" style="z-index: 5; position: absolute; cursor: pointer; margin-bottom: 30px;" src="[URL]" class="pChartPicture"></img>
			</div>
			
			<script>
			$( document ).ready(function() 
				{ 
					addImage("chart_image_full","pictureMap","[URL]" + "&getimagemap=true"); 
				});
			</script>
EOF;
			$data = str_replace("[URL]",$url,$data);
			$data = $this->col($col,$data);
			$this->temp .= $data;
		}
		/** CHART_FULL **/

		/** CHART_PREVIEW **/
		function chart_preview($url,$icon,$ID="")
		{
			$data = '<div style="cursor: pointer; float: left; margin-left: 5px; margin-right: 5px;" onclick="chart_preview(\''.$url.'\',\''.$ID.'\');" class="fa fa-'.$icon.' fa-2x"></div>';
			return $data;
		}
		/** CHART_PREVIEW **/

		/** INLINE_FORM **/
		function inline_form($url,$icon)
		{
			$data = '<div style="cursor: pointer; float: left; margin-left: 5px; margin-right: 5px;" onclick="inline_form(\''.$url.'\');" class="fa fa-'.$icon.' fa-2x"></div>';
			return $data;
		}
		/** INLINE_FORM **/

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

		/** KIOSK_PLIST **/
		function kiosk_plist($arr)
		{
			$num = 1;
			$data = '<table border="0" style="width: 100%;">';

			ksort($arr);

			/** DISPLAY_ASSIGNED **/
			while(list($sorting,$prearr) = each($arr))
			{
				while(list($SN,$_arr) = each($prearr))
				{
					$fs = false;
					while(list($k,$v) = each($_arr))
					{
						if(!$v["assigned"])
						{
							continue;
						}

						if(!$fs)
						{
							$data .= '<tr><td colspan="99" style="border: 0px;"><div style="min-height: 40px;"><img style="float: left; margin-right: 20px;" src="/admin_html/images/help.png" width="40"/><h1>'.$SN.'</h1></div></td></tr>';
							$fs = true;
						}

						$col = $num % 2;
						if($v["wait"])
						{
							$data .= '<tr class="kiosk_item" style="background-color: #888;">';
						}
						else
						{
							$data .= '<tr onclick="self.location.href=\''.$v["url"].'\';" class="kiosk_item kiosk_col_'.$col.'">';
						}

						$data .= '<td align="left" style="border: 1px solid #ccc;" ><div style="min-height: 100px;"></div></td>';
						$data .= '<td align="left" style="border: 1px solid #ccc; min-width: 350px; padding-right: 20px;" ><div style="float: left; margin-right: 10px; width: 30px; height: 100px; background-color: '.$v["action_color"].';"></div><h2 style="line-height: 70px;">'.$v["action"].'</h2></td>';
						$data .= '<td align="left" style="border: 1px solid #ccc;" width="100%"><h2>'.$v["name"].'</h2></td>';
						$data .= '<td align="left" style="border: 1px solid #ccc; min-width: 300px;"><h2>'.$v["qty"].'</h2></td>';				
						$data .= "</tr>";					

						$num++;
					}

					if($fs)
					{
						$data .= '<tr><td colspan="99" style="border: 0px;"><div style="min-height: 40px;"></div></td></tr>';
					}
				}
			}
			/** DISPLAY_ASSIGNED **/

			/** DISPLAY_NON_ASSIGNED **/
			reset($arr);
			while(list($sorting,$prearr) = each($arr))
			{
				while(list($SN,$_arr) = each($prearr))
				{
					$fs = false;
					while(list($k,$v) = each($_arr))
					{
						if($v["assigned"])
						{
							continue;
						}

						if(!$fs)
						{
							$data .= '<tr><td colspan="99" style="border: 0px;"><div style="min-height: 40px;"><h1>'.$SN.'</h1></div></td></tr>';
							$fs = true;
						}

						$col = $num % 2;
						if($v["wait"])
						{
							$data .= '<tr class="kiosk_item" style="background-color: #888;">';
						}
						else
						{
							$data .= '<tr onclick="self.location.href=\''.$v["url"].'\';" class="kiosk_item kiosk_col_'.$col.'">';
						}

						$data .= '<td align="left" style="border: 1px solid #ccc;" ><div style="min-height: 100px;"></div></td>';
						$data .= '<td align="left" style="border: 1px solid #ccc; min-width: 350px; padding-right: 20px;" ><div style="float: left; margin-right: 10px; width: 30px; height: 100px; background-color: '.$v["action_color"].';"></div><h2 style="line-height: 70px;">'.$v["action"].'</h2></td>';
						$data .= '<td align="left" style="border: 1px solid #ccc;" width="100%"><h2>'.$v["name"].'</h2></td>';
						$data .= '<td align="left" style="border: 1px solid #ccc; min-width: 300px;"><h2>'.$v["qty"].'</h2></td>';				
						$data .= "</tr>";					

						$num++;
					}

					if($fs)
					{
						$data .= '<tr><td colspan="99" style="border: 0px;"><div style="min-height: 40px;"></div></td></tr>';
					}
				}
			}
			/** DISPLAY_NON_ASSIGNED **/

			$data .= "</table>";

			$this->temp .= $this->col(12,$data);
		}		
		/** KIOSK_PLIST **/

		/** KIOSK_CLIST **/
		function kiosk_clist($arr)
		{
			$num = 1;
			$data = '<table border="0" style="width: 100%;">';
			while(list($k,$v) = each($arr))
			{
				if($v == "sep")
				{
					$data .= '<tr><td colspan="99" style="border: 0px;"><div style="min-height: 40px;"></div></td></tr>';
					continue;
				}
				$col = $num % 2;
				$data .= '<tr onclick="self.location.href=\''.$v["url"].'\';" class="kiosk_item kiosk_col_'.$col.'">';
				$data .= '<td align="left" style="border: 1px solid #ccc;" ><div style="min-height: 100px;"></div></td>';
				$data .= '<td align="left" style="border: 1px solid #ccc; padding-left: 10px; padding-right: 20px;" width="100%" ><h2>'.$v["name"].' ('.$v["max_items"].') </h2></td>';
				$data .= "</tr>";
				$num++;
			}

			$data .= "</table>";

			$this->temp .= $this->col(12,$data);
		}		
		/** KIOSK_CLIST **/

		/** KIOSK_MLIST **/
		function kiosk_mlist($arr)
		{
			$num = 1;
			$data = '<table border="0" style="width: 100%;">';
			while(list($k,$v) = each($arr))
			{
				if($v == "sep")
				{
					$data .= '<tr><td colspan="99" style="border: 0px;"><div style="min-height: 40px;"></div></td></tr>';
					continue;
				}
				$col = $num % 2;
				$data .= '<tr onclick="self.location.href=\''.$v["url"].'\';" class="kiosk_item kiosk_col_'.$col.'">';
				$data .= '<td align="left" style="border: 1px solid #ccc;" ><div style="min-height: 100px;"></div></td>';
				$data .= '<td align="left" style="border: 1px solid #ccc; padding-left: 10px; padding-right: 20px;" width="100%" ><h2>'.$v["name"].'</h2></td>';
				$data .= "</tr>";
				$num++;
			}

			$data .= "</table>";

			$this->temp .= $this->col(12,$data);
		}		
		/** KIOSK_MLIST **/

		/** KIOSK_TITLE **/
		function kiosk_title($title,$url,$nav = NULL,$active_nav = NULL)
		{
			$data = '<table border="0" style="width: 100%;">';

			$data .= "<tr>";
			$data .= '<td align="center" style="width: 100px;"><a href="'.$url.'" align="center"><img src="/admin_html/images/back.png" width="96" /></a></td>';
			$data .= '<td align="left" valign="middle"><h1>'.$title.'</h1></td>';
			if(is_array($nav))
			{
				$_nav = "";
				reset($nav);
				while(list($k,$v) = each($nav))
				{
					if($k == $active_nav)
					{
						$_nav .= '<div class="kiosk_tab kiosk_tab_active">'.$k.'</div>';
					}
					else
					{
						$_nav .= '<a href="'.$v.'"><div class="kiosk_tab">'.$k.'</div></a>';
					}
				}
				$data .= '<td align="left" valign="middle">'.$_nav.'</td>';
			}
			$data .= "</tr>";
			$data .= "</table><hr />";
			$this->temp .= $this->col(12,$data);
		}
		/** KIOSK_TITLE **/

		/** KIOSK_REPORT **/
		function kiosk_report($inital)
		{
			$tdata = '
			<form method="POST">
				<table border="0">
					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/success.png" /></td>
						<td><input id="kiosk_report_success" placeholder="0" min="0" onchange="kiosk_report();" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="success" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[SUCCESS_DESC]</h3></td>
					</tr>

					<tr>
						<td colspan="2"><div style="height: 20px;"></div></td>
					</tr>

					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/failed.png" /></td>
						<td><input id="kiosk_report_failed" placeholder="0" min="0" onchange="kiosk_report();" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="failed" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[FAILED_DESC]</h3></td>
					</tr>

					<tr>
						<td colspan="2"><div style="height: 20px;"></div></td>
					</tr>

					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/time.png" /></td>
						<td><input id="kiosk_report_time" placeholder="0" min="0" onchange="kiosk_report();" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="time" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[TIME_DESC]</h3></td>
					</tr>
				</table>
			</form>

			<script>
				var lfinal = false;
				function kiosk_report(final)
				{
					if(final == true)
					{
						if(lfinal == true)
						{
							return 0;
						}
						else
						{
							lfinal = true;
						}
					}
					else
					{
						lfinal = false;
					}

					var arg = "?kiosk_cb=" + $("#kiosk_report_success").val() + "," + $("#kiosk_report_failed").val() + "," + $("#kiosk_report_time").val();
					$("#kiosk_summary").load(arg);
				}
				
				$( document ).ready(function() {
    				kiosk_report();
				});
			</script>
			';

			$tdata = str_replace("[FAILED_DESC]",L("KIOSK_FAILED_DESC"),$tdata);
			$tdata = str_replace("[SUCCESS_DESC]",L("KIOSK_SUCCESS_DESC"),$tdata);
			$tdata = str_replace("[TIME_DESC]",L("KIOSK_TIME_DESC"),$tdata);

			$this->temp .= $this->col(4,$this->rbox(12,"primary",L("KIOSK_REPORT"),$tdata));
			$this->temp .= $this->col(5,"<div id='kiosk_summary' onmouseover='kiosk_report(true);'>".$initial."</div>");

		}
		/** KIOSK_REPORT **/

		/** KIOSK_REPORT_TIME **/
		function kiosk_report_time($inital)
		{
			$tdata = '
			<form method="POST">
				<table border="0">
					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/time.png" /></td>
						<td><input id="kiosk_report_time" placeholder="0" min="0" onchange="kiosk_report(true);" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="time" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[TIME_DESC]</h3></td>
					</tr>
				</table>
			</form>

			<script>
				var lfinal = false;
				function kiosk_report(final)
				{
					if(final == true)
					{
						if(lfinal == true)
						{
							return 0;
						}
						else
						{
							lfinal = true;
						}

					}
					else
					{
						lfinal = false;
					}

					var arg = "?kiosk_cb=" + $("#kiosk_report_time").val();
					$("#kiosk_summary").load(arg);
				}
				
				$( document ).ready(function() {
    				kiosk_report();
				});
			</script>
			';

			$tdata = str_replace("[TIME_DESC]",L("KIOSK_TIME_DESC"),$tdata);

			$this->temp .= $this->col(4,$this->rbox(12,"primary",L("KIOSK_REPORT"),$tdata));
			$this->temp .= $this->col(5,"<div id='kiosk_summary' onmouseover='kiosk_report(true);'>".$initial."</div>");

		}
		/** KIOSK_REPORT_TIME **/

		/** KIOSK_REPORT_SHIFT **/
		function kiosk_report_shift($inital)
		{
			$tdata = '
			<form method="POST">
				<table border="0">
					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/time.png" /></td>
						<td><input id="kiosk_report_shift_time" placeholder="0" min="0" onchange="kiosk_report();" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="time" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[SHIFT_TIME]</h3></td>
					</tr>
					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/coffe.png" /></td>
						<td><input id="kiosk_report_shift_overtime" placeholder="0" min="0" onchange="kiosk_report();" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="time" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[SHIFT_OVERTIME]</h3></td>
					</tr>
				</table>
			</form>

			<script>
				var lfinal = false;
				function kiosk_report(final)
				{
					if(final == true)
					{
						if(lfinal == true)
						{
							return 0;
						}
						else
						{
							lfinal = true;
						}

					}
					else
					{
						lfinal = false;
					}

					var arg = "?kiosk_cb=" + $("#kiosk_report_shift_time").val() + "," + $("#kiosk_report_shift_overtime").val();
					$("#kiosk_summary").load(arg);
				}
				
				$( document ).ready(function() {
    				kiosk_report();
				});
			</script>
			';

			$tdata = str_replace("[SHIFT_TIME]",L("SHIFT_TIME"),$tdata);
			$tdata = str_replace("[SHIFT_OVERTIME]",L("SHIFT_OVERTIME"),$tdata);

			$this->temp .= $this->col(4,$this->rbox(12,"primary",L("KIOSK_REPORT"),$tdata));
			$this->temp .= $this->col(5,"<div id='kiosk_summary' onmouseover='kiosk_report(true);'>".$initial."</div>");

		}
		/** KIOSK_REPORT_SHIFT **/


		/** KIOSK_REPORT2 **/
		function kiosk_report2($inital)
		{
			$tdata = '
			<form method="POST">
				<table border="0">
					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/success.png" /></td>
						<td><input id="kiosk_report_success" placeholder="0" min="0" onchange="kiosk_report();" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="success" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[KIOSK_PACK_DESC]</h3></td>
					</tr>

					<tr>
						<td colspan="2"><div style="height: 20px;"></div></td>
					</tr>

					<tr>
						<td style="min-width: 150px;"><img width="96" src="/admin_html/images/time.png" /></td>
						<td><input id="kiosk_report_time" placeholder="0" min="0" onchange="kiosk_report();" onkeyup="kiosk_report();" type="number" style="font-size: 50px; width: 200px;" name="time" value="" /></td>
					</tr>
					<tr>
						<td></td><td><h3>[TIME_DESC]</h3></td>
					</tr>
				</table>
			</form>

			<script>
				var lfinal = false;
				function kiosk_report(final)
				{
					if(final == true)
					{
						if(lfinal == true)
						{
							return 0;
						}
						else
						{
							lfinal = true;
						}

					}
					else
					{
						lfinal = false;
					}
					var arg = "?kiosk_cb=" + $("#kiosk_report_success").val() + "," + $("#kiosk_report_time").val();
					$("#kiosk_summary").load(arg);
				}
				
				$( document ).ready(function() {
    				kiosk_report();
				});
			</script>
			';

			$tdata = str_replace("[KIOSK_PACK_DESC]",L("KIOSK_PACK_DESC"),$tdata);
			$tdata = str_replace("[TIME_DESC]",L("KIOSK_TIME_DESC"),$tdata);

			$this->temp .= $this->col(4,$this->rbox(12,"primary",L("KIOSK_REPORT"),$tdata));
			$this->temp .= $this->col(5,"<div id='kiosk_summary' onmouseover='kiosk_report(true);'>".$initial."</div>");

		}
		/** KIOSK_REPORT2 **/

		/** KIOSK_INFO **/
		function kiosk_info($col,$title,$image)
		{
			$data = '<table border="0" style="width: 100%;">';
			$data .= "<tr>";
			$data .= '<td align="center" style="width: 100px;"><img src="/admin_html/images/'.$image.'" width="96" /></td>';
			$data .= '<td align="left" valign="middle"><h1 style="margin-left: 20px;">'.$title.'</h1></td>';
			$data .= "</tr>";
			$data .= "</table>";
			$data .= '<div style="min-height: 20px;"></div>';
			$this->temp .= $this->col($col,$data);
		}
		/** KIOSK_INFO **/		

		/** KIOSK_INFO **/
		function kiosk_small($col,$title,$image)
		{
			$data = '<table border="0" style="width: 100%;">';
			$data .= "<tr>";
			$data .= '<td align="center" style="width: 70px;"><img src="/admin_html/images/'.$image.'" width="64" /></td>';
			$data .= '<td align="left" valign="middle"><h3 style="margin-left: 20px;">'.$title.'</h3></td>';
			$data .= "</tr>";
			$data .= "</table>";
			$data .= '<div style="min-height: 20px;"></div>';
			$this->temp .= $this->col($col,$data);
		}
		/** KIOSK_INFO **/		


		/** IMAGE_SELECT_SINGLE **/
		function image_select_single($col,$name1,$title1,$url1)
		{
			$data = '<table border="0" style="width: 100%;">';

			$data .= "<tr>";
			$data .= '<td align="center"><a href="'.$url1.'" align="center"><img src="/admin_html/images/'.$name1.'" /></a></td>';
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
		function form($col,$type,$title,$submit=NULL,$cancel=NULL)
		{
			if(!$submit)
			{
				$submit = L("FORM_SUBMIT");
			}

			$this->form_submit($type,$submit);
			if($cancel)
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