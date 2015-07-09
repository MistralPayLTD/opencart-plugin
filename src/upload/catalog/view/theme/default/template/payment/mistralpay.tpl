<form method="post" action="<?php echo $server_response->{'url'}; ?>">
    <input name="key" type="hidden" value="<?php echo $server_response->{'key'}; ?>"/>
<div class="buttons">
  <div class="pull-right">
    <button 
    	type="submit" 
    	id="button-confirm" 
    	class="btn btn-primary" 
	>
    	<?php echo $button_confirm; ?>
   </button>
  </div>
</div>
</form>
    