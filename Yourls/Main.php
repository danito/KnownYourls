<?php

namespace IdnoPlugins\Yourls {

    class Main extends \Idno\Common\Plugin {

        function registerPages() {
           // todo: register admin settings
   
            // Handle shorten event
            \Idno\Core\site()->addEventHook('url/shorten', function(\Idno\Core\Event $event) {

                //todo: get this from admin page
                // adapt this to your needs, see your yourls tool page
            $access_token = "yourls_secret_token";
            $yourls_api = "http://your_yourls_domain/yourls-api.php";
            error_log("what happens here?", 0);
                if ($access_token) {

                    $url = $event->response();

                    try {
                        $result = \Idno\Core\Webservice::get($yourls_api, [
                                    'signature' => $access_token,
                                    'action' => 'shortenurl',
                                    'url' => $url,
                                    'format' => 'json'
                        ]);

                       $result = json_decode($result['content']);
                       // print_r($result);
                        if ($result && $result->status_txt == 'OK') {
                            $event->setResponse($result->data->url);
                        } else
                            throw new \Exception("There was a problem shortening that link...");
                    } catch (\Exception $e) {
                        \Idno\Core\site()->session()->addMessage($e->getMessage(), 'alert-warn');
                    }
                }
            });
        }

    }

}
