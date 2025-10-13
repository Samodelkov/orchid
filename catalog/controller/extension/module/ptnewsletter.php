<?php
class ControllerExtensionModulePtnewsletter extends Controller
{
    public function index($setting) {
        $this->load->language('tmarket/module/ptnewsletter');

        $data = array();

        if (isset($setting['popup']) && $setting['popup']) {
            $data['popup'] = true;
        } else {
            $data['popup'] = false;
        }

        $this->document->addScript('catalog/view/javascript/tmarket/newsletter/mail.js');

        return $this->load->view('tmarket/module/ptnewsletter', $data);
    }
}
