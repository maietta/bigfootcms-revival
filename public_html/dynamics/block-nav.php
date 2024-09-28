<!--<script type="text/javascript" src="/js/hover-aware2.js"></script>-->
<style>
/*#utility {
	width:100%;
	min-width:1160px;
	height:80px;
	background:url(http://gocountry105.com/images/global/opaque/85-red.png);
	border-top:1px solid #000;
	border-bottom:1px solid #666;
	box-shadow: 0 -10px 20px #000;
	z-index:9999;
}
#utilityContent {
	width:1160px;
	margin:0 auto;
}
#block-nav {
	
}
.block-nav {
	list-style: none;
	position: relative;
	margin: 0;
	padding: 0;
}*/
.block-nav {
	/*width:275px;
	float: left;
	background: #fff;
	padding: 0;
	position: relative;*/
	box-shadow: 0 1px 3px rgba(0,0,0,0.1);
	border:1px solid red;
}
.block-nav img {
	display: block;
	position: relative;
	border:1px solid blue;
}
.block-nav img:hover {
	display:none;
	position:absolute;
	left:-900000;
}
#hide-nav {
}
#hide-nav {
	display:block;
	background: #333;
	background: rgba(75,75,75,0.9);
	border:1px solid orange;
}
#hide-nav:hover {
	display: block;
	/*height:150px;
	padding: 10px 0;
	margin: 40px 20px 20px 20px;*/
	text-transform: uppercase;
	font-weight: normal;
	font-size: 16px;
	line-height: 24px;
	text-align:center;
	color: rgba(255,255,255,0.9);
	text-shadow: 1px 1px 1px rgba(0,0,0,0.2);
	border-bottom: 1px solid rgba(255,255,255,0.5);
	box-shadow: 0 1px 0 rgba(0,0,0,0.1), 0 -10px 0 rgba(255,255,255,0.3);
	border:1px solid green;
}
/*.block-nav div span:hover {
	box-shadow: 0 1px 0 rgba(255,217,0,1), 0 -10px 0 rgba(255,217,0,1);
}*/	
.block-nav a {
	color: #fff;
	padding:2px;
	text-decoration:none;
	display:block;
	width:100%;
}
.block-nav a:hover {
	color: #000;
	background:#ffd900;
}

</style>
<script>
$(function() {
  // $(' #hide-nav > span ').each(function() {
  //     $(this).hoverdir();
  // });
   //$(' #navThisWeek > div ').each(function() {
   //    $(this).hoverdir();
   //});
   //$('#toggleNav').click(function() {
    //   $('#navThisWeek').slideToggle(300);
     //  return false;
   //});
   //$("#navThisWeek").hide();
   //$(".scroll").click(function(event) {
    //   event.preventDefault();
    //   $('html,body').animate({
     //      scrollTop: $(this.hash).offset().top
    //   }, 500);
   //});
});
</script>
<div id="block-nav">
<!-- opening container for the blocks -->
<div class="row">
	<div class="large-4 columns small-12 block-nav">
  		<img src="http://gocountry105.com/images/global/nav/1-v1.png" alt="Listen" />
    	<div class="large-12 columns small-12 " id="hide-nav">
        		<a href="http://gocountry105.com/programming/listen/">listen</a>
		  		<a href="http://gocountry105.com/programming/features/theMorningShow/">graham in the mornings</a>
    	  		<a href="http://gocountry105.com/programming/hosts/">air staff</a>
      			<a href="http://gocountry105.com/programming/schedule/">schedule</a>
      			<a href="http://gocountry105.com/programming/playlist/">playlist</a>
   		</div>
	</div>
  	<div class="large-4 columns small-12 block-nav">
    	<img src="http://gocountry105.com/images/global/nav/2-v1.png" alt="Win" />
    	<div class="large-12 columns small-12" id="hide-nav">
            	<a href="http://gocountry105.com/contests/onair/">on air contests</a>
				<a href="http://gocountry105.com/contests/online/">online contests</a>
        </div>
  	</div>
  	<div class="large-4 columns small-12 block-nav">
    	<img src="http://gocountry105.com/images/global/nav/3-v1.png" alt="Rewards" />
		<div class="large-12 columns small-12" id="hide-nav">
            	<a href="http://rewards.gocountry105.com">rewards</a>
				<a href="http://rewards.gocountry105.com/asp3/lwcodes.aspx">secret password</a>
				<a href="http://rewards.gocountry105.com/asp3/stationcodes.aspx">bonus codes</a>
				<a href="http://rewards.gocountry105.com/MusicTestDisplay.aspx">music survey</a>
       </div>
	</div>
</div>
<!--<div class="row">
  <div class="large-4 columns small-12 block-nav"> <b> <img src="http://gocountry105.com/images/global/nav/4-v3.png" alt="The Morning Show" />
    <div><span><a href="http://gocountry105.com/programming/features/theMorningShow/#tms">Find out what happened on the show today</a>
      </span></div>
    </b>
  </div>
  <div class="large-4 columns small-12 columns block-nav"> <b> <img src="http://gocountry105.com/images/global/nav/5-v1.png" alt="Media" />
    <div><span><a href="http://gocountry105.com/media/video/">video</a>
      <a href="http://gocountry105.com/media/photo/gallery/">photo</a>
      <a href="http://gocountry105.com/media/audio/">audio</a>
      </span></div>
    </b>
  </div>
  <div class="large-4 columns small-12 columns block-nav"> <b> <img src="http://gocountry105.com/images/global/nav/6-v3.png" alt="The Morning Show" />
    <div><span><a href="http://gocountry105.com/calendars/events/">event calendar</a>
      <a href="http://gocountry105.com/calendars/concert/">concert calendar</a>
      <a href="http://gocountry105.com/calendars/community/">community calendar</a>
      </span></div>
    </b>
  </div>
</div>-->
<!-- Closing container for the blocks -->
</div>
