<?php

class LaughStubPlugin {
	
	public function LaughStubPlugin() {
	}
	
	function control(){
		$data = get_option('LaughStubPlugin');
		?>
        <div id="ls_control_panel">
            <p>
                <label for="LaughStubPlugin_title">Title</label>
                <input name="LaughStubPlugin_title" id="LaughStubPlugin_title" type="text" value="<?php echo $data['title']; ?>" size="30" />
            </p>
            <p>
                <label for="LaughStubPlugin_maxShows">No. of Shows</label>
                <input name="LaughStubPlugin_maxShows" id="LaughStubPlugin_maxShows" type="text" value="<?php echo $data['maxShows']; ?>" />
                
            </p>
            <p>
                <label for="LaughStubPlugin_ls_url">Venue or Comedian URL</label>
                <span class="small">www.laughstub.com/</span><input name="LaughStubPlugin_ls_url" size="12" type="text" value="<?php echo $data['ls_url']; ?>" />
                <p class="small">eg: irvineImprov or Tony-Rock</p>
            </p>
            <p>
                <label for="LaughStubPlugin_affiliateKey">Affiliate Key</label>
                <input name="LaughStubPlugin_affiliateKey" id="LaughStubPlugin_affiliateKey" type="text" value="<?php echo $data['affiliateKey']; ?>" />
            </p>
            <p>
        		Please contact <a href="mailto:info@laughstub.com">LaughStub</a> to know your Venue or Comedian URL. You can also get Affiliate Key to promote sales through us.
        	</p>
      	</div>
		  <?php
		   if (isset($_POST['LaughStubPlugin_title'])){
			$data['title'] = attribute_escape($_POST['LaughStubPlugin_title']);
			$data['maxShows'] = attribute_escape($_POST['LaughStubPlugin_maxShows']);
			$data['ls_url'] = attribute_escape($_POST['LaughStubPlugin_ls_url']);
			$data['affiliateKey'] = attribute_escape($_POST['LaughStubPlugin_affiliateKey']);
			update_option('LaughStubPlugin', $data);
		  }

	}
	
	function widget($args){
		$data = get_option('LaughStubPlugin');
		$title = $data['title'] ;
		if(strlen($title) == 0) {
			$title = 'LS Upcoming Shows' ;
		}
		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
		echo ls_getUpcomingShows() ;
		echo $args['after_widget'];
	}

	function register(){
		register_sidebar_widget('LaughStub.com', array('LaughStubPlugin', 'widget'));
		register_widget_control('LaughStub.com', array('LaughStubPlugin', 'control'));
	}
	
	function activate(){
		$data = array( 'maxShows' => 5 ,'title' => 'LS Upcoming Shows', 'ls_url' => '', 'affiliateKey' => '');
		if (!get_option('LaughStubPlugin')){
		  add_option('LaughStubPlugin' , $data);
		} else {
		  update_option('LaughStubPlugin' , $data);
		}
	}
	
	function deactivate(){
		delete_option('LaughStubPlugin');
	}
	
	function addHeaderCode() {
		echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/laughstub/css/ls.css" />' . "\n";
	}

}

function ls_getUpcomingShows() {
	$data = get_option('LaughStubPlugin');
	 
	$ls_user_url = $data['ls_url'];
	$maxShows = $data['maxShows'];
	$affiliateKey = $data['affiliateKey'];
	
	$rss_url = 'http://www.laughstub.com/tickets/'. $ls_user_url ;
	
	if(strlen($affiliateKey) > 0) {
		$rss_url = $rss_url . '?affiliateKey=' . $affiliateKey ;
	}
	
	if(strlen($ls_user_url) > 0) {
	
		try {
			$doc = new DOMDocument();
			$doc->load($rss_url);
			
			$arrFeeds = array();
			$shows = "<ul>" ;
			
			$count = 0 ;
			foreach ($doc->getElementsByTagName('item') as $node) {
				if($count < $maxShows) {
					$shows = $shows . '<li>' ;
					$shows = $shows . '<span class="ls_showName"><a target="_blank" href="' . $node->getElementsByTagName('guid')->item(0)->nodeValue . '">' . $node->getElementsByTagName('title')->item(0)->nodeValue . '</a></span>' ;
					
					$showDate = rtrim($node->getElementsByTagName('pubDate')->item(0)->nodeValue) ;
					$len = strlen($showDate) ;
					$len = $len - 4 ;
					$showDate = substr($showDate,0,$len) ;
					$showDate = strtotime($showDate) ;
					
					$shows = $shows . "<span class='ls_showTime'>" . date("D, d M, g:i A",$showDate) . "</span>";
					
					$thisDesc = $node->getElementsByTagName('description')->item(0)->nodeValue ;
					$thisDesc = preg_replace("/<[^>]*>/","",$thisDesc) ;
					
					if(strlen($thisDesc) > 80) {
						$thisDesc = substr($thisDesc,0,77) ;
						$thisDesc = $thisDesc . "..." ;
					}
					if(strlen($thisDesc) > 0) {
						$shows = $shows . '<p class="ls_showDesc">' . $thisDesc . '</p>' ;
					}
					
					$shows = $shows . "<p class='ls_showVenue'>" . $node->getElementsByTagName('venue')->item(0)->nodeValue  . "</p>" ;
					
					$shows = $shows . '<a target="_blank" class="buy" href="' . $node->getElementsByTagName('guid')->item(0)->nodeValue . '">Buy&nbsp;Tickets</a>' ;
					$shows = $shows .  '</li>' ;
					$count = $count + 1 ;
				}
			}
			
			$shows = $shows . '</ul>' ;
			
			$retval = '' ;
			$retval .= '<div id="ls_upcoming_shows">' ;  
			$retval .= $shows ;  
			$retval .= '</div>' ; 
		}
		catch(Exception $e) {
			$retval = '<div id="ls_upcoming_shows"><p>Error loading Shows</p><p>' . $e->getMessage() . '</p></div>' ;
		}
	}
	else {
		$retval = '<div id="ls_upcoming_shows"><p>No Upcoming Shows</p></div>' ;
	}
	return $retval ;
}

?>