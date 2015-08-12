<style>.custom-combobox{display:inline}.custom-combobox-toggle{position:absolute;top:0;bottom:0;margin-left:-1px;padding:0}.custom-combobox-input{margin:0;padding:5px 10px}.ui-state-default,.ui-widget-content .ui-state-default,.ui-widget-header .ui-state-default{background:#fafafa!important}.ui-helper-hidden-accessible{display:none}</style>
<script>/*<![CDATA[*/jQuery(function(){jQuery.widget("custom.combobox",{_create:function(){this.wrapper=jQuery("<span>").addClass("custom-combobox").attr("style","margin-top:-4px;").insertAfter(this.element);this.element.hide();this._createAutocomplete();this._createShowAllButton()},_createAutocomplete:function(){var a=this.element.children(":selected"),a=a.val()?a.text():"";this.label=jQuery("<label>").appendTo(this.wrapper).attr("style","display:inline;font-weight: bold").text("By Merchant ");this.input=jQuery("<input>").appendTo(this.wrapper).val(a).attr("name","merchant").attr("title","").attr("style","width:15%;padding:5px;background-color:#fafafa").addClass("").autocomplete({delay:0,minLength:0,source:jQuery.proxy(this,"_source"),messages:{noResults:"",results:function(){}}});this._on(this.input,{autocompleteselect:function(c,b){b.item.option.selected=!0;this._trigger("select",c,{item:b.item.option})},autocompletechange:"_removeIfInvalid"})},_createShowAllButton:function(){var a=this.input,b=!1;jQuery("<a>").attr("tabIndex",-1).attr("style","padding:7.5px 4px;margin-top:-1px;background-color:#fafafa").tooltip().appendTo(this.wrapper).button({icons:{primary:"ui-icon-triangle-1-s"},text:!1}).removeClass("ui-corner-all").mousedown(function(){b=a.autocomplete("widget").is(":visible")}).click(function(){a.focus();b||a.autocomplete("search","")})},_source:function(b,c){var a=new RegExp(jQuery.ui.autocomplete.escapeRegex(b.term),"i");c(this.element.children("option").map(function(){var d=jQuery(this).text();if(this.value&&(!b.term||a.test(d))){return{label:d,value:d,option:this}}}))},_removeIfInvalid:function(d,e){if(!e.item){var a=this.input.val(),c=a.toLowerCase(),b=!1;this.element.children("option").each(function(){if(jQuery(this).text().toLowerCase()===c){return this.selected=b=!0,!1}});b||(this.input.val("").attr("title",a+" didn't match any item").tooltip("open"),this.element.val(""),this._delay(function(){this.input.tooltip("close").attr("title","")},2500),this.input.autocomplete("instance").term="")}},_destroy:function(){this.wrapper.remove();this.element.show()}})});jQuery(function(){jQuery("#combobox").combobox();jQuery("#toggle").click(function(){jQuery("#combobox").toggle()})});jQuery(function(){jQuery("#s.prosper_field").blur(function(){jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#s.prosper_field").val(),enableFacets:"merchant",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#combobox").get(0).options.length=0;jQuery.each(a.facets.merchant,function(c,b){jQuery("#combobox").get(0).options[jQuery("#combobox").get(0).options.length]=new Option(b.value,b.value)})},error:function(){alert("Failed to load merchants")}})})});/*]]>*/</script>
<?php if(!$options['noSearchBar']):?>
<div class="prosper_searchform"
	style="background: #ddd; display: inline; width: 100%; padding: 4px 0 2px 0; font-size: 12px; text-align: center">
	<form id="prosperSearchForm" class="searchform" method="POST" action=""
		rel="nolink" name="prosperentSearchAllForm" style="width: 100%">
		<label style="display: inline; font-weight: bold">Search</label> <?php if (count($typeSelector) > 1): ?> <select
			style="display: inline; overflow: hidden; margin: 0; width: auto; background-color: #fafafa; padding: 6px; min-width: 175px"
			name="type"> <?php foreach($typeSelector as $i=>$ends){?> <option
				<?php echo($params['type']==$i?'selected="selected"':'');?>
				value="<?php echo $i;?>"><?php echo $ends;?></option> <?php }?> </select> <?php endif;?> <label
			style="display: inline; font-weight: bold">For</label> <input id="s"
			style="width: 15%; background-color: #fafafa; padding: 6.5px"
			class="<?php echo($type=='celebrity'?'prosper_celeb_field prosper_field':'prosper_field');?>"
			value="<?php echo($query?$query:'');?>" type="text"
			name="<?php echo $searchPost?$searchPost:'q';?>"
			placeholder="<?php echo isset($options['Search_Bar_Text'])?$options['Search_Bar_Text']:($searchTitle?'Search '.$searchTitle:'Search Products');?>"> <?php if($filterArray):?> <select
			id="combobox"
			style="display: inline; overflow: hidden; margin: 0; background-color: #fafafa"
			name="merchant"> <?php $allFilters=array_merge(array(''=>''),$mainFilters['merchant'],$secondaryFilters['merchant']);foreach($allFilters as $t=>$filter){?> <option
				<?php echo($params['merchant']==rawurlencode($t)?'selected="selected"':'');?>
				value="<?php echo $t;?>"><?php echo $t;?></option> <?php }?> </select> <?php endif;?> <label
			for="PriceSort" style="display: inline; font-weight: bold">Sort By</label>
		<select name="sort"
			style="margin: 0; display: inline; width: auto; background-color: #fafafa; padding: 6px; min-width: 160px"> <?php foreach($sortArray as $i=>$sort){?> <option
				<?php echo(rawurldecode($params['sort'])==$sort?'selected="selected"':'');?>
				value="<?php echo $sort;?>"><?php echo $i;?></option> <?php }?> </select>
		<input class="prosper_submit" type="submit" name="submit" value="GO">
	</form>
</div>
<div class="clear"></div>
<?php
	if ($noResults)
	{
		echo '<div class="noResults">No Results</div>';

		if (($params['brand'] || $params['merchant']) && $query)
		{
			echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('/merchant/' . $params['merchant'], '/brand/' . $params['brand']), '', $url) . '>clear the filter(s)</a>.</div>';
		}
		else
		{
			echo '<div class="noResults-secondary">' . ($type == 'celebrity' ? 'Please search for a celebrity' : 'Please try your search again.') . '</div>';
		}
		echo '<div class="noResults-padding"></div>';
	}
endif;if(!$params['view']||$params['view']==='list'){?>
<div id="productList" style="width: 100%; float: right"> <?php if(!empty($results)){$resultsCount = count($results);foreach($results as $i=>$record){if(is_ssl()){$record['image_url']=str_replace('http','https',$record['image_url']);}$cid=$type==='coupon'?$record['couponId']:($type==='local'?$record['localId']:$record['catalogId']);?> <div
		class="productBlock" <?php echo ($i == ($resultsCount - 1) ? 'style="border-bottom:none;"' : ''); ?>>
		<div class="productImage">
			<a
				href=<?php echo($options['gotoMerchantBypass']?'"'.$record['affiliate_url'].'&interface=wp&subinterface=prospershop" target="'.$target.'"':'"'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'"');?>
				rel="nolink"><span
				<?php echo($type==='coupon'?'class="loadCoup"':'class="load"');?>><img
					src="<?php echo $record['image_url'];?>"
					title="<?php echo $record['keyword'];?>"
					alt="<?php echo $record['keyword'];?>" /></span></a>
		</div>
		<div class="productContent"> <?php if($record['promo']){echo '<div class="promo" ' . (($record['expiration_date'] || $record['expirationDate']) ? 'style="width:auto"' : '') . '><span>'.$record['promo'].'</span></div>'.(($record['expiration_date']||$record['expirationDate'])?'&nbsp;&nbsp;&mdash;&nbsp;&nbsp;':'');}if($record['expiration_date']||$record['expirationDate']){$expirationDate=$record['expirationDate']?$record['expirationDate']:$$record['expiration_date'];$expires=strtotime($expirationDate);$today=strtotime(date("Y-m-d"));$interval=($expires - $today)/(60*60*24);if($interval<=20&&$interval>0){echo '<div class="couponExpire"><span>'.$interval.' Day'.($interval>1?'s':'').' Left!</span></div>';}elseif($interval<=0){echo '<div class="couponExpire"><span>Ends Today!</span></div>';}else{echo '<div class="couponExpire"><span>Expires Soon!</span></div>';}}?> <div
				class="productTitle">
				<a
					href=<?php echo($options['gotoMerchantBypass']?'"'.$record['affiliate_url'] . '&interface=wp&subinterface=prospershop" target="'.$target.'"':'"'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'"');?>
					rel="nolink"><span><?php echo preg_replace('/\(.+\)/i','',$record['keyword']);?></span></a>
			</div> <?php if($type!='coupon'&&$type!='local'){?> <div
				class="productDescription"> <?php if(strlen($record['description'])>200){echo substr($record['description'],0,200).'...';}else{echo $record['description'];}?> </div> <?php }if($record['coupon_code']){echo '<div class="couponCode">Coupon Code: <span class="code_cc"><a href="' . $record['affiliate_url'] . '">'.$record['coupon_code'].'</a></span></div>';}?> <div
				class="productBrandMerchant"> <?php if($record['brand']){echo '<span class="brandIn"><u>Brand</u>: '.(!(preg_match('/\b' . $record['brand'] . '\b/i', rawurldecode($params['brand'])))?'<a href="'.str_replace('/page/'.$params['page'],'',$url).'/brand/'.rawurlencode($record['brand']).'" rel="nolink"><cite>'.$record['brand'].'</cite></a>':$record['brand']).'</span>';}if($record['state']||$record['city']||$record['zipCode']&&!$params['zipCode']){$city=((!$params['city']||$noResults)?'<a href="'.str_replace(array('/page/'.$params['page'],'/city/'.$filterCity),array('',''),$url).'/city/'.rawurlencode($record['city']).'" rel="nolink"><cite>'.ucwords($record['city']).'</cite></a>':$record['city']).($record['state']?', ':'');$state=(!$params['state']||$noResults)?'<a href="'.str_replace(array('/page/'.$params['page'],'/state/'.$filterState),array('',''),$url).'/state/'.rawurlencode($record['state']).'" rel="nolink"><cite>'.($record['city']?strtoupper($record['state']):ucwords($backStates[$record['state']])).'</cite></a> ':($record['city']?strtoupper($record['state']):ucwords($record['state'])).'&nbsp;';$zip=(!$params['zip']||$noResults)?' <a href="'.str_replace(array('/page/'.$params['page'],'/zip/'.$filterZip),array('',''),$url).'/zip/'.rawurlencode($record['zipCode']).'" rel="nolink"><cite>'.$record['zipCode'].'</cite></a>':$record['zipCode'];echo '<span class="brandIn" style="display:inline-block;"><u>Location</u>: '.$city.$state.$zip.'</span>';}if($record['merchant']){echo '<span class="merchantIn"><u>Merchant</u>: '.(!(preg_match('/\b' . $record['merchant'] . '\b/i', rawurldecode($params['merchant'])))?'<a href="'.str_replace('/page/'.$params['page'],'',$url).'/merchant/'.rawurlencode($record['merchant']).'" rel="nolink"><cite>'.$record['merchant'].'</cite></a>':$record['merchant']).'</span>';}?> </div>
		</div>
		<div class="productEnd"> <?php if($record['price_sale']||$record['price']||$record['priceSale']){$priceSale=$record['priceSale']?$record['priceSale']:$record['price_sale'];if(empty($priceSale)||$record['price']<=$priceSale){?> <div
				class="productPriceNoSale">
				<span><?php echo($currency=='GBP'?'&pound;':'$').number_format($record['price'], 2);?></span>
			</div> <?php }else{?> <div class="productPrice">
				<span><?php echo($currency=='GBP'?'&pound;':'$').number_format($record['price'], 2)?></span>
			</div>
			<div class="productPriceSale">
				<span><?php echo($currency=='GBP'?'&pound;':'$').number_format($priceSale, 2)?></span>
			</div> <?php }}?>
			<div class="shopCheck prosperVisit">
				<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>
			</div>
		</div>
	</div> <?php }}}elseif($params['view']==='grid'){$gridImage=($options['Grid_Img_Size']?preg_replace('/px|em|%/i','',$options['Grid_Img_Size']):200).'px';if($trend){$gridImage=($options['Same_Img_Size']?preg_replace('/px|em|%/i','',$options['Same_Img_Size']):200).'px';}$classLoad=($type==='coupon'||$gridImage<120)?'class="loadCoup"':'class="load"';echo '<div id="simProd" style="width:100%;float:right;border-top: 2px solid #ddd;">';echo '<ul>';if(!empty($results)){foreach($results as $record){if(is_ssl()){$record['image_url']=str_replace('http','https',$record['image_url']);}$priceSale=$record['priceSale']?$record['priceSale']:$record['price_sale'];$price=$priceSale?'<div class="prodPriceSale">'.($currency=='GBP'?'&pound;':'$').number_format($priceSale, 2).'</div>':'<div class="prodPrice">'.($currency=='GBP'?'&pound;':'$').number_format($record['price'], 2).'</div>';$keyword=preg_replace('/\(.+\)/i','',$record['keyword']);$cid=$type==='coupon'?$record['couponId']:($type==='local'?$record['localId']:$record['catalogId']);?> <li
		<?php echo 'style="width:'.$gridImage.'!important;"';?>>
		<div class="listBlock">
			<div class="prodImage">
				<a
					href=<?php echo($options['gotoMerchantBypass']?'"'.$record['affiliate_url'] . '&interface=wp&subinterface=prospershop" target="'.$target.'"':'"'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'"');?>
					rel="nolink"><span
					<?php echo $classLoad.($type!='coupon'?('style="width:'.$gridImage.'!important; height:'.$gridImage.'!important;"'):'style="height:60px;width:120px;margin:0 15px"');?>><img
						<?php echo($type!='coupon'?('style="width:'.$gridImage.'!important; height:'.$gridImage.'!important;"'):'style="height:60px;width:120px;"');?>
						src="<?php echo $record['image_url'];?>"
						title="<?php echo $record['keyword'];?>"
						alt="<?php echo $record['keyword'];?>" /></span></a>
			</div> <?php if($record['promo']){echo '<div class="promo"><span><a href="'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'" rel="nolink">'.$record['promo'].'!</a></span></div>';}elseif($record['expiration_date']||$record['expirationDate']){$expirationDate=$record['expirationDate']?$record['expirationDate']:$record['expiration_date'];$expires=strtotime($expirationDate);$today=strtotime(date("Y-m-d"));$interval=($expires - $today)/(60*60*24);if($interval<=20&&$interval>0){echo '<div class="couponExpire"><span><a href="'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'" rel="nolink">'.$interval.' Day'.($interval>1?'s':'').' Left!</a></span></div>';}elseif($interval<=0){echo '<div class="couponExpire"><span><a href="'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'" rel="nolink">Ends Today!</a></span></div>';}else{echo '<div class="couponExpire"><span><a href="'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'" rel="nolink">Expires Soon!</a></span></div>';}}elseif($type=='coupon'||$type=='local'){echo '<div class="promo">&nbsp;</div>';}?> <div
				class="prodContent">
				<div class="prodTitle">
					<a
						href=<?php echo($options['gotoMerchantBypass']?'"'.$record['affiliate_url'] . '&interface=wp&subinterface=prospershop" target="'.$target.'"':'"'.$homeUrl.'/'.$type.'/'.rawurlencode(str_replace('/',',SL,',$record['keyword'])).'/cid/'.$cid.'"');?>
						rel="nolink"> <?php echo $keyword;?> </a>
				</div> <?php if($price&&$type!='coupon'&&$type!='local'){echo $price;}?> </div>
			<div class="shopCheck prosperVisit">
				<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>
			</div>
		</div>
	</li> <?php }}echo '</ul>';}$this->searchModel->prosperPagination($totalAvailable,$params['page']);echo '</div>';echo '</br>';