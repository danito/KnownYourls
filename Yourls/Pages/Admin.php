<?php

    /**
     * Plugin administration
     */

    namespace IdnoPlugins\Yourls\Pages {

        /**
         * Default class to serve the homepage
         */
        class Admin extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t = \Idno\Core\site()->template();
                $body = $t->draw('admin/yourls');
                $t->__(array('title' => 'Yourls', 'body' => $body))->drawPage();
            }

            function postContent() {
                $this->adminGatekeeper(); // Admins only
                $secret_token = trim($this->getInput('secret_token'));
                $yourls_api = trim($this->getInput('yourls_api'));
                \Idno\Core\site()->config->config['yourls'] = array(
                    'secret_token' => $secret_token,
                    'yourls_api' => $yourls_api
                );
                \Idno\Core\site()->config()->save();
                \Idno\Core\site()->session()->addMessage('Your Yourls application details were saved.');
                $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'admin/yourls/');
            }

        }

    }