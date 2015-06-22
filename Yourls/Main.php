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

            \Idno\Core\site()->addEventHook('syndicate', function(\Idno\Core\Event $event) {
                error_log("Syndicate it", 4);
                //  echo "<pre>";     var_dump($event);
            });
            // Listen to link text expand
            \Idno\Core\site()->addEventHook('url/expandintext', function(\Idno\Core\Event $event) {

                if ($object = $event->data()['object']) {

                    if ($owner = $object->getOwner()) {

                        if (1) {

                            // Get body
                            $body = $event->response();
                             // Load/create lookup table
                            $urls = $object->url_expansion_lookup;
                            if (!$urls)
                                $urls = [];
                            // Find urls
                            if (preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $body, $matches)) {
                                foreach ($matches[0] as $match) {
                                    $longurl = false;
                                    if (isset($urls[$match]) && !empty($urls[$match])) {
                                        $longurl = $urls[$match];
                                    } else {
                                        // We haven't encountered this url, so try and expand it
                                        // get response from yourls
                                        if (!$longurl && chkYourls()) {

                                            $yourls_api = \Idno\Core\site()->config->config['yourls']['yourls_api'];
                                            $yourls_secret_token = \Idno\Core\site()->config->config['yourls']['secret_token'];

                                            $result = \Idno\Core\Webservice::get($yourls_api, [
                                                        'signature' => $yourls_secret_token,
                                                        'action' => 'expand',
                                                        'shorturl' => $match,
                                                        'format' => 'json'
                                            ]);
                                            $result = json_decode($result['content']);
                                            if (isset($result->longurl))
                                                $longurl = $result->longurl;
                                        }
                                    }

                                    // Save expanded form, or that we failed.
                                    $urls[$match] = $longurl;

                                    // Now do a replace
                                    if ($longurl !== false) {
                                        $body = str_replace($match, $longurl, $body);
                                    }
                                }
                            }

                            // Save updated urls list
                            if (!empty($urls)) {
                                $object->url_expansion_lookup = $urls;
                                $object->save();
                            }

                            // Update body
                            $event->setResponse($body);
                        }
                    }
                }
            });


            // Handle shorten event
            \Idno\Core\site()->addEventHook('url/shorten', function(\Idno\Core\Event $event) {

                if (chkYourls()) {
                    $url = $event->response();
                    $yourls_api = \Idno\Core\site()->config->config['yourls']['yourls_api'];
                    $yourls_secret_token = \Idno\Core\site()->config->config['yourls']['secret_token'];

                    try {
                        $result = \Idno\Core\Webservice::get($yourls_api, [
                                    'signature' => $yourls_secret_token,
                                    'action' => 'shorturl',
                                    'url' => $url, 'format' => 'json'
                        ]);
                        error_log("shorten result " . json_encode($result));
                        $result = json_decode($result['content']);
                        $status = $result->statusCode ;
                        error_log("status == ".$status);
                        if ($result && $status === 200) {
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

