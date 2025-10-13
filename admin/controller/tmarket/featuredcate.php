<?php
class ControllerTmarketFeaturedcate extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('tmarket/featuredcate');
        $this->load->language('tmarket/adminmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('catalog/category');

        $this->load->model('tmarket/featuredcate');

        $this->model_tmarket_featuredcate->createFeaturedCate();

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        /* Begin breadcrumb */
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );
        /* End */

        /* Get Categories */
        $this->load->model('tool/image');

        $data['categories'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $category_total = $this->model_catalog_category->getTotalCategories();

        $results = $this->model_tmarket_featuredcate->getCategories($filter_data);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['secondary_image'])) {
                $secondary_image = $this->model_tool_image->resize($result['secondary_image'], 40, 40);
            } else {
                $secondary_image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            if (is_file(DIR_IMAGE . $result['alternative_image'])) {
                $alternative_image = $this->model_tool_image->resize($result['alternative_image'], 40, 40);
            } else {
                $alternative_image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            $data['categories'][] = array(
                'category_id'           => $result['category_id'],
                'name'                  => $result['name'],
                'sort_order'            => $result['sort_order'],
                'secondary_image'       => $secondary_image,
                'alternative_image'     => $alternative_image,
                'is_featured'           => $result['is_featured'],
                'edit'                  => $this->url->link('tmarket/featuredcate/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url, true),
            );
        }
        /* End */

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->session->data['information'])) {
            $data['information'] = $this->session->data['information'];

            unset($this->session->data['information']);
        } else {
            $data['information'] = '';
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_sort_order'] = $this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $category_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['tmarket_menus'] = array();

        if($this->user->hasPermission('access', 'extension/module/ptcontrolpanel')) {
            $data['tmarket_menus'][] = array(
                'title'  => '<i class="a fa fa-magic"></i> ' . $this->language->get('text_control_panel'),
                'url'    => $this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'tmarket/module')) {
            $data['tmarket_menus'][] = array(
                'title'  => '<i class="a fa fa-puzzle-piece"></i> ' . $this->language->get('text_theme_module'),
                'url'    => $this->url->link('tmarket/module', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'tmarket/featuredcate')) {
            $data['tmarket_menus'][] = array(
                'title'  => '<i class="a fa fa-tag"></i> ' . $this->language->get('text_special_category'),
                'url'    => $this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 1
            );
        }

        if($this->user->hasPermission('access', 'tmarket/ultimatemenu')) {
            $data['tmarket_menus'][] = array(
                'title'  => '<i class="a fa fa-bars"></i> ' . $this->language->get('text_ultimate_menu'),
                'url'    => $this->url->link('tmarket/ultimatemenu/menuList', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        
            $blog_menu = array();

            if ($this->user->hasPermission('access', 'tmarket/blog/post')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts'),
                    'url'    => $this->url->link('tmarket/blog/post', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'tmarket/blog/list')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts_list'),
                    'url'    => $this->url->link('tmarket/blog/list', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'tmarket/blog/setting')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_blog_setting'),
                    'url'    => $this->url->link('tmarket/blog/setting', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if($blog_menu) {
                $data['tmarket_menus'][] = array(
                    'title'  => '<i class="a fa fa-ticket"></i> ' . $this->language->get('text_blog'),
                    'child'  => $blog_menu,
                    'active' => 0
                );
            }


        if($this->user->hasPermission('access', 'tmarket/slider')) {
            $data['tmarket_menus'][] = array(
                'title'  => '<i class="a fa fa-film"></i> ' . $this->language->get('text_slider'),
                'url'    => $this->url->link('tmarket/slider', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'tmarket/testimonial')) {
            $data['tmarket_menus'][] = array(
                'title'  => '<i class="a fa fa-comment"></i> ' . $this->language->get('text_testimonial'),
                'url'    => $this->url->link('tmarket/testimonial', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'tmarket/newsletter')) {
            $data['tmarket_menus'][] = array(
                'title'  => '<i class="a fa fa-envelope"></i> ' . $this->language->get('text_newsletter'),
                'url'    => $this->url->link('tmarket/newsletter', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        $this->document->addStyle('view/stylesheet/tmarket/themeadmin.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tmarket/featuredcate/list', $data));
    }

    public function edit() {
        $this->load->language('tmarket/featuredcate');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('tmarket/featuredcate');

        $category_id = $this->request->get['category_id'];

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_tmarket_featuredcate->editFeaturedCate($category_id, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    protected function getForm() {
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['action'] = $this->url->link('tmarket/featuredcate/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $this->request->get['category_id'] . $url, true);
        $data['cancel'] = $this->url->link('tmarket/featuredcate', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $category_info = $this->model_tmarket_featuredcate->getCategory($this->request->get['category_id']);

        $data['user_token'] = $this->session->data['user_token'];

        $data['category_name'] = $category_info['name'];

        if (isset($this->request->post['secondary_image'])) {
            $data['secondary_image'] = $this->request->post['secondary_image'];
        } elseif (!empty($category_info)) {
            $data['secondary_image'] = $category_info['secondary_image'];
        } else {
            $data['secondary_image'] = '';
        }

        if (isset($this->request->post['alternative_image'])) {
            $data['alternative_image'] = $this->request->post['alternative_image'];
        } elseif (!empty($category_info)) {
            $data['alternative_image'] = $category_info['alternative_image'];
        } else {
            $data['alternative_image'] = '';
        }

        if (isset($this->request->post['is_featured'])) {
            $data['is_featured'] = $this->request->post['is_featured'];
        } elseif (!empty($category_info)) {
            $data['is_featured'] = $category_info['is_featured'];
        } else {
            $data['is_featured'] = 0;
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['secondary_image']) && is_file(DIR_IMAGE . $this->request->post['secondary_image'])) {
            $data['secondary_img'] = $this->model_tool_image->resize($this->request->post['secondary_image'], 100, 100);
        } elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['secondary_image'])) {
            $data['secondary_img'] = $this->model_tool_image->resize($category_info['secondary_image'], 100, 100);
        } else {
            $data['secondary_img'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if (isset($this->request->post['alternative_image']) && is_file(DIR_IMAGE . $this->request->post['alternative_image'])) {
            $data['alternative_img'] = $this->model_tool_image->resize($this->request->post['alternative_image'], 100, 100);
        } elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['alternative_image'])) {
            $data['alternative_img'] = $this->model_tool_image->resize($category_info['alternative_image'], 100, 100);
        } else {
            $data['alternative_img'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->document->addStyle('view/stylesheet/tmarket/themeadmin.css');
        $this->document->addScript('view/javascript/tmarket/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/tmarket/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tmarket/featuredcate/form', $data));

    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('tmarket/featuredcate');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort'        => 'name',
                'order'       => 'ASC',
                'start'       => 0,
                'limit'       => 5
            );

            $results = $this->model_tmarket_featuredcate->getFeaturedCategories($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'tmarket/featuredcate')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
