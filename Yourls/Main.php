<?php

namespace IdnoPlugins\Yourls {

    class Main extends \Idno\Common\Plugin {

        function registerPages() {

            // Register admin settings
            \Idno\Core\site()->addPageHandler('admin/yourls', '\IdnoPlugins\Yourls\Pages\Admin');
            /** Template extensions */
            // Add menu items to account & administration screens
            \Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'admin/yourls/menu');

            /**
             * check if Yourls api and secret are filled in
             * @return bolean
             */
            function chkYourls() {
                if (!empty(\Idno\Core\site()->config->config['yourls']['yourls_api']) || !empty(\Idno\Core\site()->config->config['yourls']['secret_token'])) {
                    return true;
                } else {
                    return FALSE;
                }
            }

            // Handle shorten event
            \Idno\Core\site()->addEventHook('url/shorten', function(\Idno\Core\Event $event) {

                if (chkYourls()) {
                    $url = $event->response();
                    $yourls_api = \Idno\Core\site()->config->config['yourls']['yourls_api'];
                    $yourls_secret_token = \Idno\Core\site()->config->config['yourls']['secret_token'];
                    
                    try {
			$result = \Idno\Core\Webservice::get($yourls_api, [
				    'signature' => $yourls_secret_token,
				    'action' => 'shortenurl',
				'url'=> $url, 'format'=>'json'
			]);

			$result = json_decode($result['content']);

			if ($result && $result->status == 'success') {
			    $event->setResponse($result->shorturl);
			} else
			    throw new \Exception("There was a problem shortening that link...");
		    } catch (\Exception $e) {
			\Idno\Core\site()->session()->addMessage($e->getMessage(), 'alert-warn');
		    }
                }
            }, 4);
        }

    }

}

