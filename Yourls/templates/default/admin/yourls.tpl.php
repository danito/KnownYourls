<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?=$this->draw('admin/menu')?>
        <h1>Yourls configuration</h1>

    </div>

</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <form action="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/yourls/" class="form-horizontal" method="post">
            <div class="control-group">
                <div class="controls-config">
                    <p>
                        To use your own Yourl url shortener, we need to know your secret signature token and the yourls api address.</p>
                    <p>
                        You find both under admin/tools. More explanation on <a href="http://yourls.org/#API" target="_blank">http://yourls.org/#API</a>
                    </p>
                    
                </div>
            </div>
                        
            <div class="controls-group">
	                <p>
                       Fill in the details below:
                    </p>
                <label class="control-label" for="secret-token">Secret token</label>

                    <input type="text" id="secret-token" placeholder="secret token" class="form-control" name="secret_token" value="<?=htmlspecialchars(\Idno\Core\site()->config()->yourls['secret_token'])?>" >


            
                <label class="control-label" for="yourls-api">Url to yourls-api.php</label>

                    <input type="text" id="yourls-api" placeholder="Your site/yourls-api.php" class="form-control" name="yourls_api" value="<?=htmlspecialchars(\Idno\Core\site()->config()->yourls['yourls_api'])?>" >
   
            </div>     	            
          <div class="controls-group">
	          

          </div>  
            
            <div>
                <div class="controls-save">
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/admin/twitter/')?>
        </form>
    </div>
</div>
