<?php
class Pages_autocomplete_acc {

/**
 * Pages Autocomplete accessory
 *
 * @package		Pages Autocomplete
 * @category	Accessories
 * @author		Iain Urquhart
 * @copyright	All rights reserved
 * @link		http://iain.co.nz
 */

	var $name			= 'Pages Autocomplete';
	var $id				= 'pages_autocomplete';
	var $version		= '1.0.1';
	var $description	= 'Adds autocomplete action to pages_uri field';
	var $sections		= array();

	function __construct()
	{
		$this->EE =& get_instance();
	}

	function set_sections()
	{
		
		// cheers to @leevigraham for this one.
		$this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.pages_autocomplete").parent().remove();</script>';
		
		$this->EE->cp->add_js_script(
		    array("ui" => array('core', 'widget', 'position', 'autocomplete'))
		);
		$this->EE->javascript->compile(); 
		
		// are we publishing an entry?
		if($this->EE->input->get('C') == 'content_publish')
		{
			// get our site pages together
			$site_pages = $this->EE->config->item('site_pages');
			$site_id = $this->EE->config->item('site_id');
			$search_array = '';
			
			// have we got some?
			if(isset($site_pages[$site_id]['uris']))
			{
				$pages = $site_pages[$site_id]['uris'];
				
				// build our js list of pages for autocompete
				foreach($pages as $page_uri)
				{
					// remove trailing slashes if they exist, as we force them on below
					$page_uri = rtrim($page_uri, '/');
					$search_array .= '"'.$page_uri;
					
					// add the trailing slash, but not to the home page if it's just '/'
					if($page_uri != '/')
					{
						$search_array .= "/";
					}
					$search_array .= "\",\n";
				}
			}
			
			$search_array = rtrim($search_array, ',');
			
			// print_r($site_pages);
			
			$this->EE->cp->add_to_head('
			<script type="text/javascript">
				$(function() {
					var available_pages = [
						'.$search_array.'
					];
										
					var last_seg = "";
					$("input#pages__pages_uri").autocomplete({
						source: available_pages,
						minLength: 0,
						delay: 0,
						open: function(event, ui)
						{
							$(".ui-autocomplete").prepend("<li class=\"pages_select_parent_label\">Select a parent, type to filter:</li>");
						},
						select: function(event, ui)
						{
							var existing_uri = $("#pages__pages_uri").val();
							if(existing_uri != "/example/pages/uri/")
							{
								var url_title = $("input#url_title").val();
								var last_seg = prompt("Final url segment for this page:",url_title);
								ui.item.value = ui.item.value + last_seg;
							}
						}
					});
					
					// force the select options to open on click
					$("input#pages__pages_uri").click(function() {
						
						var existing_uri = $("#pages__pages_uri").val();
						if(existing_uri == "")
						{
							$(this).val("/").trigger("keydown");
						}
					
					});
					
					
				});
			
			</script>
			<style type="text/css">
				
				/* 
					anyone know why the folks at Ellislab dont load ui css files with the plugins?
				*/
			
				.ui-autocomplete { position: absolute; cursor: default; background: #fff; font-size: 12px; 
				-webkit-box-shadow: 0 0 10px rgba(0,0,0,0.3);
				-moz-box-shadow: 0 0 10px rgba(0,0,0,0.3);
				-o-box-shadow: 0 0 10px rgba(0,0,0,0.3);
				box-shadow: 0 0 10px rgba(0,0,0,0.3);
				 height: 200px; overflow-y: scroll; overflow-x: hidden;
				}	
				
				/* workarounds */
				* html .ui-autocomplete { width:1px; } /* without this, the menu expands to 100% in IE6 */
				
				.pages_select_parent_label { padding: 5px 3px; color: #bababa;}
				
				.ui-menu {
					list-style:none;
					padding: 2px;
					margin: 0;
					display:block;
					float: left;
				}
				.ui-menu .ui-menu {
					margin-top: -3px;
				}
				.ui-menu .ui-menu-item {
					margin:0;
					padding: 0;
					zoom: 1;
					float: left;
					clear: left;
					width: 100%;
				}
				.ui-menu .ui-menu-item a {
					text-decoration:none;
					display:block;
					padding:.2em .4em;
					line-height:1.5;
					zoom:1;
				}
				.ui-menu .ui-menu-item a:hover {cursor:pointer}
				.ui-menu .ui-menu-item a.ui-state-hover,
				.ui-menu .ui-menu-item a.ui-state-active {
					font-weight: normal;
					margin: -1px;
				}
			</style>
			');
		}

	}
	
}
// END CLASS