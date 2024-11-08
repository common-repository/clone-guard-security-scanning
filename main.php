<?php
/*
 * Plugin Name: CloneGuard Security Scanning
 * Description: Secure your website with direct access to CloneGuard Security Compliance, Scanning and Verification servers.
 * Author: Clone Systems, Inc.
 * Author URI: https://www.clone-systems.com
 * Version: 2.5
 */

defined('ABSPATH') || exit;

require_once(__DIR__ . '/classes/class-clone-guard-api.php');
require_once(__DIR__ . '/classes/class-clone-guard-widget.php');

class Clone_Guard_Security_Scanning {
    public $key = 'cgss';
    public $key_ = 'cgss_';
    public $version = '2.5';

    public $feedback_url = 'https://pciscan.clone-systems.com/downloads/ASV-Feedback-Form.pdf';

    // The hook for the setting basic page.
    public $hook_overview;
    public $hook_scans;
    public $hook_reports;
    public $hook_vulnerabilities;
    public $hook_options;
    public $hook_settings;

    public $scans = [];

    public $userDetails = [];
    public $appType = '';

    // The class constructor.
    public function __construct() {
        add_action('init', [$this, 'init']);

        add_action('widgets_init', [$this, 'widgetsInit']);
    }

    // Load the asset files for specific admin pages.
    public function adminEnqueueScripts($hook) {
        if($this->hook_overview == $hook) {
            wp_enqueue_style($this->key_ . 'admin_general', plugins_url('css/admin_general.css', __FILE__), [], $this->version);
            wp_enqueue_style($this->key_ . 'admin_overview', plugins_url('css/admin_overview.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'datetimepicker', plugins_url('js/jquery.datetimepicker.full.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'datetimepicker', plugins_url('css/jquery.datetimepicker.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'select2', plugins_url('js/select2.4.0.13.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'select2', plugins_url('css/select2.4.0.13.min.css', __FILE__), [], $this->version);
            
            wp_enqueue_script($this->key_ . 'admin_scan', plugins_url('js/admin_scan.js', __FILE__), ['jquery'], $this->version);
        }
        
        if($this->hook_scans == $hook) {
            wp_enqueue_script($this->key_ . 'datetimepicker', plugins_url('js/jquery.datetimepicker.full.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'datetimepicker', plugins_url('css/jquery.datetimepicker.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'select2', plugins_url('js/select2.4.0.13.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'select2', plugins_url('css/select2.4.0.13.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'admin_ajax_form', plugins_url('js/admin_ajax_form.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_script($this->key_ . 'admin_scan', plugins_url('js/admin_scan.js', __FILE__), ['jquery'], $this->version);

            wp_enqueue_style($this->key_ . 'admin_general', plugins_url('css/admin_general.css', __FILE__), [], $this->version);
        }

        if($this->hook_reports == $hook) {
            wp_enqueue_script($this->key_ . 'datetimepicker', plugins_url('js/jquery.datetimepicker.full.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'datetimepicker', plugins_url('css/jquery.datetimepicker.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'select2', plugins_url('js/select2.4.0.13.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'select2', plugins_url('css/select2.4.0.13.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'admin_scan', plugins_url('js/admin_scan.js', __FILE__), ['jquery'], $this->version);

            wp_enqueue_style($this->key_ . 'admin_general', plugins_url('css/admin_general.css', __FILE__), [], $this->version);
        }

        if($this->hook_vulnerabilities == $hook) {
            wp_enqueue_script($this->key_ . 'datetimepicker', plugins_url('js/jquery.datetimepicker.full.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'datetimepicker', plugins_url('css/jquery.datetimepicker.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'select2', plugins_url('js/select2.4.0.13.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'select2', plugins_url('css/select2.4.0.13.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'admin_scan', plugins_url('js/admin_scan.js', __FILE__), ['jquery'], $this->version);

            wp_enqueue_script($this->key_ . 'admin_ajax_form', plugins_url('js/admin_ajax_form.js', __FILE__), ['jquery'], $this->version);

            wp_enqueue_style($this->key_ . 'admin_general', plugins_url('css/admin_general.css', __FILE__), [], $this->version);
            wp_enqueue_style($this->key_ . 'admin_vulnerability', plugins_url('css/admin_vulnerability.css', __FILE__), [], $this->version);
        }

        if($this->hook_options == $hook) {
            wp_enqueue_script($this->key_ . 'datetimepicker', plugins_url('js/jquery.datetimepicker.full.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'datetimepicker', plugins_url('css/jquery.datetimepicker.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'select2', plugins_url('js/select2.4.0.13.min.js', __FILE__), ['jquery'], $this->version);
            wp_enqueue_style($this->key_ . 'select2', plugins_url('css/select2.4.0.13.min.css', __FILE__), [], $this->version);

            wp_enqueue_script($this->key_ . 'admin_scan', plugins_url('js/admin_scan.js', __FILE__), ['jquery'], $this->version);

            wp_enqueue_script($this->key_ . 'admin_ajax_form', plugins_url('js/admin_ajax_form.js', __FILE__), ['jquery'], $this->version);

            wp_enqueue_style($this->key_ . 'admin_general', plugins_url('css/admin_general.css', __FILE__), [], $this->version);
            wp_enqueue_style($this->key_ . 'admin_options', plugins_url('css/admin_options.css', __FILE__), [], $this->version);
        }

        if($this->hook_settings == $hook) {
            wp_enqueue_script($this->key_ . 'admin_ajax_form', plugins_url('js/admin_ajax_form.js', __FILE__), ['jquery'], $this->version);

            wp_enqueue_style($this->key_ . 'admin_general', plugins_url('css/admin_general.css', __FILE__), [], $this->version);
            wp_enqueue_style($this->key_ . 'admin_settings', plugins_url('css/admin_settings.css', __FILE__), [], $this->version);
        }
    }

    // Create a link to one of the plugin admin pages.
    public function adminLink($page = 'scans', $key = '', $subkey = '', $paged = 1) {
        if(is_numeric($key)) {
            $url = admin_url('admin.php?page=' . $this->key_ . $page . '&paged=' . $key);
        } elseif($subkey) {
            $url = admin_url('admin.php?page=' . $this->key_ . $page . '&key=' . $key . '&subkey=' . $subkey . '&paged=' . $paged);
        } elseif($key) {
            $url = admin_url('admin.php?page=' . $this->key_ . $page . '&key=' . $key);
        } else {
            $url = admin_url('admin.php?page=' . $this->key_ . $page);
        } 
        $url = esc_url($url);
        return $url;
    }

    /**
     * Creates the multipagination url params in the options page.
     *
     * @since 1.8
     * 
     * https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/
     *
     * @param string $type Schedules, Targets or Notificatoins.
     * @param integer $page The page number of the current type.
     * @return string The url with the params.
     */
    public function optionsPaginationLink($type, $page) {
        if ($type == 'schedules') { 
            $url = admin_url('admin.php?page=cgss_options&' . $type . 'Paged=' . $page); // Value of string: '&schedulesPaged=X'
            if (isset($_GET['targetsPaged'])) {
                $url = $url . '&targetsPaged=' . $_GET['targetsPaged'];
            }
            if (isset($_GET['notificationsPaged'])) {
                $url = $url . '&notificationsPaged=' . $_GET['notificationsPaged'];
            }
        } elseif ($type == 'targets') {
            $url = admin_url('admin.php?page=cgss_options&' . $type . 'Paged=' . $page); // Value of string: '&targetsPaged=X'
            if (isset($_GET['schedulesPaged'])) {
                $url = $url . '&schedulesPaged=' . $_GET['schedulesPaged'];
            }
            if (isset($_GET['notificationsPaged'])) {
                $url = $url . '&notificationsPaged=' . $_GET['notificationsPaged'];
            }
        } elseif ($type == 'notifications') {
            $url = admin_url('admin.php?page=cgss_options&' . $type . 'Paged=' . $page); // Value of string: '&notificationsPaged=X'
            if (isset($_GET['schedulesPaged'])) {
                $url = $url . '&schedulesPaged=' . $_GET['schedulesPaged'];
            }
            if (isset($_GET['targetsPaged'])) {
                $url = $url . '&targetsPaged=' . $_GET['targetsPaged'];
            }
        } else {
            $url = admin_url('admin.php?page=cgss_options');
        }
        $url = esc_url($url);
        return $url;
    }

    // Create the pages for the admin menu.
    public function adminMenu() {
        add_menu_page('CloneGuard Security', 'CloneGuard Security', 'manage_options', $this->key_ . 'overview', false, 'dashicons-shield-alt');
        $this->hook_overview = add_submenu_page($this->key_ . 'overview', 'Overview', 'Overview', 'manage_options', $this->key_ . 'overview', [$this, 'adminOverview']);
        $this->hook_scans = add_submenu_page($this->key_ . 'overview', 'Scans', 'Scans', 'manage_options', $this->key_ . 'scans', [$this, 'adminScans']);
        $this->hook_reports = add_submenu_page($this->key_ . 'overview', 'Reports', 'Reports', 'manage_options', $this->key_ . 'reports', [$this, 'adminReports']);
        $this->hook_vulnerabilities = add_submenu_page($this->key_ . 'overview', 'Vulnerabilities', 'Vulnerabilities', 'manage_options', $this->key_ . 'vulnerabilities', [$this, 'adminVulnerabilities']);
        $this->hook_options = add_submenu_page($this->key_ . 'overview', 'Options', 'Options', 'manage_options', $this->key_ . 'options', [$this, 'adminOptions']);
        $this->hook_settings = add_submenu_page($this->key_ . 'overview', 'Settings', 'Settings', 'manage_options', $this->key_ . 'settings', [$this, 'adminSettings']);
    }

    // Output the admin Overview page.
    public function adminOverview() {
        global $cloneGuardSecurityAPI;

        $action = $this->key_ . 'overview';
        $title = 'CloneGuard Security Scanning';

        $nonce_update_app_type = wp_create_nonce($this->key_ . 'update_user_app_type');

        $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];
        $this->userDetails['pciAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['pci'];
        $this->userDetails['vrmsAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['vrms'];
        $this->userDetails['penetrationAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['penetration'];

        if ($app_type == 'penetration') {
            $this->hook_vulnerabilities = add_submenu_page($this->key_ . 'overview', 'Findings', 'Findings', 'manage_options', $this->key_ . 'vulnerabilities', [$this, 'adminVulnerabilities']);
        } 
        // else {
        //     $this->hook_vulnerabilities = add_submenu_page($this->key_ . 'overview', 'Vulnerabilities', 'Vulnerabilities', 'manage_options', $this->key_ . 'vulnerabilities', [$this, 'adminVulnerabilities']);
        // }

        $page = 1;

        $reports = $cloneGuardSecurityAPI->getReports($page, 5);

        $scans = $cloneGuardSecurityAPI->getScans($page);
        $last_reports_ids = [];
        foreach($scans['scans'] as $scan) {
            array_push($last_reports_ids, $scan['last_report']['id']);
        }

        $last_reports = [];
        $last_reports_statuses = [];
        $vulnerabilities_sum = 0;
        foreach($last_reports_ids as $id) {
            $current_report = $cloneGuardSecurityAPI->getReport($id);
            array_push($last_reports, $current_report);
            if (isset($current_report['results_count']['high_and_medium'])) {
                if($current_report['results_count']['high_and_medium'] == 0) {
                    array_push($last_reports_statuses, 'pass');
                } elseif ($current_report['results_count']['high_and_medium'] > 0) {
                    array_push($last_reports_statuses, 'fail');
                    $vulnerabilities_sum = $vulnerabilities_sum + $current_report['results_count']['high_and_medium'];
                }
            }
        }

        if(in_array("fail", $last_reports_statuses)) {
            $compliance_status = 'fail';
        } else {
            $compliance_status= 'pass';
        }

        include 'views/admin_overview.php';
    }

    // Output the admin scans page.
    public function adminScans() {
        global $cloneGuardSecurityAPI;
        $action = $this->key_ . 'scans';
        $title = 'Scans';

        if(isset($_GET['key'])) {
            $key = sanitize_text_field($_GET['key']);
        } else {
            $key = '';
        }

        if(isset($_GET['subkey'])) {
            $subkey = sanitize_text_field($_GET['subkey']);
        } else {
            $subkey = '';
        }

        if($key == 'scan-create') {
            $action = $this->key_ . 'scan_create';
            $url_back = $this->adminLink('scans');

            $scan = [];
            $scan['id'] = 'scan-create';
            $scan['name'] = '';
            $scan['schedule'] = [];
            $scan['schedule']['id'] = '';
            $scan['target'] = [];
            $scan['target']['id'] = '';
            $scan['notifications'] = [];
            $scan['notifications']['id'] = '';
            $scan['notification_list'] = [];
            $scan['scanner'] = [];
            $scan['scanner']['id'] = '';
            $scan['config'] = [];
            $scan['config']['id'] = '';
            $scan['comment'] = '';

            $schedules = $cloneGuardSecurityAPI->getAllSchedules();
            $targets = $cloneGuardSecurityAPI->getAllTargets();
            $notifications = $cloneGuardSecurityAPI->getAllNotifications();
            $scanners = $cloneGuardSecurityAPI->getScanners();
            $configs = $cloneGuardSecurityAPI->getScanConfigs();
            
            $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];

            if(isset($_GET['return']) && $_GET['return'] == 'yes') {
                $user_id = get_current_user_id();
                $scans = get_user_meta($user_id, $this->key_ . 'scans_temp_save', true);

                if(isset($scans[$scan['id']])) {
                    $scan['name'] = $scans[$scan['id']]['name'];
                    if(isset($scan['schedule'])) {
                        $scan['schedule']['id'] = $scans[$scan['id']]['schedule'];
                    }
                    if(isset($scan['target'])) {
                        $scan['target']['id'] = $scans[$scan['id']]['target'];
                    }
                    if(isset($scan['notifications'])) {
                        $scan['notification_list'] = explode(',', $scans[$scan['id']]['notifications']);
                    }
                    // TODO
                    // if ($app_type == 'vrms' || $app_type == 'penetration') {
                    //     if(isset($scan['scanner'])) {
                    //         $scan['scanner']['id'] =  $scans[$scan['id']]['scanner'];
                    //     }
                    //     if(isset($scan['config'])) {
                    //         $scan['config']['id'] =  $scans[$scan['id']]['config'];
                    //     }
                    // }
                    $scan['comment'] = $scans[$scan['id']]['comment'];
                }
            }

            $nonce_scan_temp_save = wp_create_nonce($this->key_ . 'scan_temp_save');

            include 'views/admin_scan_create.php';
        } elseif($key == 'schedule-create' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'schedule_create';
            $last_key = sanitize_text_field($_GET['subkey']);
            $url_back = $this->adminLink('scans', $last_key);
            $url_back .= '&return=yes';

            include 'views/admin_schedule_create.php';
        } elseif($key == 'target-create' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'target_create';
            $last_key = sanitize_text_field($_GET['subkey']);
            $url_back = $this->adminLink('scans', $last_key);
            $url_back .= '&return=yes';

            include 'views/admin_target_create.php';
        } elseif($key == 'notification-create' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'notification_create';
            $last_key = sanitize_text_field($_GET['subkey']);
            $url_back = $this->adminLink('scans', $last_key);
            $url_back .= '&return=yes';

            include 'views/admin_notification_create.php';
        } elseif($key) {
            $action = $this->key_ . 'scan_update';
            $url_back = $this->adminLink('scans');

            $scan = $cloneGuardSecurityAPI->getScan($key);
            $notifications = $cloneGuardSecurityAPI->getAllNotifications();
            $targets = $cloneGuardSecurityAPI->getAllTargets();
            $schedules = $cloneGuardSecurityAPI->getAllSchedules();
            
            $this->userDetails['appType'] = $cloneGuardSecurityAPI->getUserDetails()['app_type'];
            $configs = $cloneGuardSecurityAPI->getScanConfigs();
            $scanners = $cloneGuardSecurityAPI->getScanners();

            $scan['notification_list'] = [];
            if(count($scan['notifications'])) {
                foreach($scan['notifications'][0] as $notification) {
                    $scan['notification_list'][] = $notification['id'];
                }
            }

            if(isset($_GET['return']) && $_GET['return'] == 'yes') {
                $user_id = get_current_user_id();
                $scans = get_user_meta($user_id, $this->key_ . 'scans_temp_save', true);

                if(isset($scans[$scan['id']])) {
                    $scan['name'] = $scans[$scan['id']]['name'];
                    if(isset($scan['schedule'])) {
                        $scan['schedule']['id'] = $scans[$scan['id']]['schedule'];
                    }
                    if(isset($scan['target'])) {
                        $scan['target']['id'] = $scans[$scan['id']]['target'];
                    }
                    if(isset($scan['notifications'])) {
                        $scan['notification_list'] = explode(',', $scans[$scan['id']]['notifications']);
                    }
                    // TODO
                    // if ($app_type == 'vrms' || $app_type == 'penetration') {
                    //     if(isset($scan['scanner'])) {
                    //         $scan['scanner']['id'] =  $scans[$scan['id']]['scanner'];
                    //     }
                    //     if(isset($scan['config'])) {
                    //         $scan['config']['id'] =  $scans[$scan['id']]['config'];
                    //     }
                    // }
                    $scan['comment'] = $scans[$scan['id']]['comment'];
                }

            }

            $nonce_scan_temp_save = wp_create_nonce($this->key_ . 'scan_temp_save');

            include 'views/admin_scan_edit.php';
        } else {
            $url_current = $this->adminLink('scans');
            $nonce_scan_action = wp_create_nonce($this->key_ . 'scan_action');
            $nonce_scan_delete = wp_create_nonce($this->key_ . 'scan_delete');
            $nonce_update_app_type = wp_create_nonce($this->key_ . 'update_user_app_type');

            if(isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                $page = $_GET['paged'];
            } else {
                $page = 1;
            }

            $scans = $cloneGuardSecurityAPI->getScans($page);
            $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];
            $this->userDetails['pciAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['pci'];
            $this->userDetails['vrmsAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['vrms'];
            $this->userDetails['penetrationAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['penetration'];

            include 'views/admin_scans.php';
        }
    }

    // Output the admin reports page.
    public function adminReports() {
        global $cloneGuardSecurityAPI;
        $action = $this->key_ . 'reports';
        $title = 'Reports';

        if(isset($_GET['key'])) {
            $key = sanitize_text_field($_GET['key']);
        } else {
            $key = '';
        }

        if(isset($_GET['subkey'])) {
            $subkey = sanitize_text_field($_GET['subkey']);
        } else {
            $subkey = '';
        }

        if(isset($_GET['paged'])) {
            $paged = sanitize_text_field($_GET['paged']);
        } else {
            $paged = 1;
        }

        if($key == 'report-view' && $subkey) {
            if($paged > 1) {
                $url_back = $this->adminLink('reports', $paged);
            } else {
                $url_back = $this->adminLink('reports');
            }
            $nonce_report_download = wp_create_nonce($this->key_ . 'report_download');

            $report = $cloneGuardSecurityAPI->getReport($subkey);

            $report['date'] = date('D M j Y G:i:s', strtotime($report['name']));
            $report['quarter'] = $this->getQuarter($report['name']);
            if(isset($report['task']) && isset($report['task']['name'])) {
                $report['scan_name'] = $report['task']['name'];
            } else {
                $report['scan_name'] = '';
            }
            if(isset($report['results_count']) && isset($report['results_count']['high_and_medium'])) {
                $report['display_vulnerabilities'] = $report['results_count']['high_and_medium'];
            } else {
                $report['display_vulnerabilities'] = '';
            }
            if(isset($report['compliance']) && $report['compliance']) {
                $report['display_compliance'] = '<span class="icon_pass">Pass</span>';
            } else {
                $report['display_compliance'] = '<span class="icon_fail">Fail</span>';
            }
            if(isset($report['files']) && isset($report['files']['attestation'])) {
                $report['attestation_status'] = 'generated';
            } else {
                $report['attestation_status'] = 'ungenerated';
            }
            if(isset($report['files']) && isset($report['files']['detailed'])) {
                $report['detailed_status'] = 'generated';
            } else {
                $report['detailed_status'] = 'ungenerated';
            }
            if(isset($report['files']) && isset($report['files']['executive'])) {
                $report['executive_status'] = 'generated';
            } else {
                $report['executive_status'] = 'ungenerated';
            }
            if(isset($report['files']) && isset($report['files']['remediation'])) {
                $report['remediation_status'] = 'generated';
            } else {
                $report['remediation_status'] = 'ungenerated';
            }
            $report['feedback_status'] = 'generated';

            include 'views/admin_report_view.php';
        } else {
            $url_current = $this->adminLink('reports');
            $nonce_report_delete = wp_create_nonce($this->key_ . 'report_delete');
            $nonce_update_app_type = wp_create_nonce($this->key_ . 'update_user_app_type');

            if(isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                $page = $_GET['paged'];
            } else {
                $page = 1;
            }
            $reports = $cloneGuardSecurityAPI->getReports($page);

            foreach($reports['reports'] as $key => $report) {
                $reports['reports'][$key]['date'] = date('D M j Y G:i:s', strtotime($report['name']));
                $reports['reports'][$key]['quarter'] = $this->getQuarter($report['name']);
                if(isset($report['task']) && isset($report['task']['name'])) {
                    $reports['reports'][$key]['scan_name'] = $report['task']['name'];
                } else {
                    $reports['reports'][$key]['scan_name'] = '';
                }
                if(isset($report['results']) && isset($report['results']['high_and_medium'])) {
                    $reports['reports'][$key]['display_vulnerabilities'] = $report['results']['high_and_medium'];
                } else {
                    $reports['reports'][$key]['display_vulnerabilities'] = '';
                }
                if(isset($report['compliance']) && $report['compliance']) {
                    $reports['reports'][$key]['display_compliance'] = '<div style="color:#64b450;"><span class="dashicons dashicons-yes"></span>Pass</div>';
                } else {
                    $reports['reports'][$key]['display_compliance'] = '<div style="color:#dc3232;"><span class="dashicons dashicons-no"></span>Fail</div>';
                }
            }

            $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];
            $this->userDetails['pciAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['pci'];
            $this->userDetails['vrmsAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['vrms'];
            $this->userDetails['penetrationAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['penetration'];

            include 'views/admin_reports.php';
        }
    }

    // Output the vulnerabilities page.
    public function adminVulnerabilities() {
        global $cloneGuardSecurityAPI;
        $action = $this->key_ . 'vulnerabilities';
        $title = 'Vulnerabilities';

        if(isset($_GET['key'])) {
            $key = sanitize_text_field($_GET['key']);
        } else {
            $key = '';
        }

        if(isset($_GET['subkey'])) {
            $subkey = sanitize_text_field($_GET['subkey']);
        } else {
            $subkey = '';
        }

        if(isset($_GET['paged'])) {
            $paged = sanitize_text_field($_GET['paged']);
        } else {
            $paged = 1;
        }

        if($key == 'vulnerability-view' && isset($_GET['subkey'])) {
            if($paged > 1) {
                $url_back = $this->adminLink('vulnerabilities', $paged);
            } else {
                $url_back = $this->adminLink('vulnerabilities');
            }

            $vulnerability = $cloneGuardSecurityAPI->getResult($subkey);

            $cve = $vulnerability['nvt']['cve'];
            $cve_links = array_map('trim', explode(",", $cve)); // https://stackoverflow.com/questions/19347005/how-can-i-explode-and-trim-whitespace

            // String parser for nvt.tags object.
            $tags = $vulnerability['nvt']['tags'];
            $seperated_strings = explode("|", $tags);

            $index = 0;
            foreach($seperated_strings as $string) {
                if (strpos($string, 'summary=') !== false) {
                    $summary = $seperated_strings[$index];
                    $summary = substr($summary, strpos($summary, '=') + 1);
                } elseif (strpos($string, 'affected=') !== false) {
                    $affected = $seperated_strings[$index];
                    $affected = substr($affected, strpos($affected, '=') + 1);
                } elseif (strpos($string, 'solution=') !== false) {
                    $solution = $seperated_strings[$index];
                    $solution = substr($solution, strpos($solution, '=') + 1);
                } elseif (strpos($string, 'qod_type=') !== false) {
                  $qod_type = $seperated_strings[$index];
                    $qod_type = substr($qod_type, strpos($qod_type, '=') + 1);
                } elseif (strpos($string, 'impact=') !== false) {
                    $impact = $seperated_strings[$index];
                    $impact = substr($impact, strpos($impact, '=') + 1);
                } elseif (strpos($string, 'insight=') !== false) {
                    $insight = $seperated_strings[$index];
                    $insight = substr($insight, strpos($insight, '=') + 1);
                } elseif (strpos($string, 'vuldetect=') !== false) {
                    $vuldetect = $seperated_strings[$index];
                    $vuldetect = substr($vuldetect, strpos($vuldetect, '=') + 1);
                } elseif (strpos($string, 'solution_type=') !== false) {
                    $solution_type = $seperated_strings[$index];
                    $solution_type = substr($solution_type, strpos($solution_type, '=') + 1);
                }
                $index++;
            }

            include 'views/admin_vulnerability_view.php';
        } 
        elseif($key == 'vulnerability-add-exception' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'exception_create';
            if($paged > 1) {
                $url_back = $this->adminLink('vulnerabilities', $paged);
            } else {
                $url_back = $this->adminLink('vulnerabilities');
            }

            $vulnerability = $cloneGuardSecurityAPI->getResult($subkey);
            
            include 'views/admin_vulnerability_add_exception.php';
        }
        elseif($key == 'vulnerability-edit-exception' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'exception_update';
            if($paged > 1) {
                $url_back = $this->adminLink('vulnerabilities', $paged);
            } else {
                $url_back = $this->adminLink('vulnerabilities');
            }

            $vulnerability = $cloneGuardSecurityAPI->getResult($subkey);
            $exception = $cloneGuardSecurityAPI->getException($vulnerability['overrides']['id'])[0];
            $exception = $exception[array_key_first($exception)];

            include 'views/admin_vulnerability_edit_exception.php';
        }
        else {
            $url_current = $this->adminLink('vulnerabilities');
            $nonce_update_app_type = wp_create_nonce($this->key_ . 'update_user_app_type');

            if(isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                $page = $_GET['paged'];
            } else {
                $page = 1;
            }
            
            $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];
            $this->userDetails['pciAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['pci'];
            $this->userDetails['vrmsAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['vrms'];
            $this->userDetails['penetrationAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['penetration'];

            $vulnerabilities = $cloneGuardSecurityAPI->getResults($page);

            // Count how many vulnerabilities have exceptions. 
            $count_exceptions = 0;
            foreach($vulnerabilities['results'] as $key => $vul) {
                if(isset($vul['overrides']['status'])) {
                    $count_exceptions = $count_exceptions + 1;
                }
            }

            // If all the vulnerabilities have exceptions, then hide the 'Add Exception' option.
            if($count_exceptions == count($vulnerabilities['results'])) {
                $hide_add_exception_option = true;
            } else {
                $hide_add_exception_option = false;
            }

            include 'views/admin_vulnerabilities.php';
        }
    }

    // Output the admin options page.
    public function adminOptions() {
        global $cloneGuardSecurityAPI;
        $action = $this ->key_ . 'options';
        $title = 'Options';

        if(isset($_GET['key'])) {
            $key = sanitize_text_field($_GET['key']);
        } else {
            $key = '';
        }

        if(isset($_GET['subkey'])) {
            $subkey = sanitize_text_field($_GET['subkey']);
        } else {
            $subkey = '';
        }

        if($key == 'schedule-create') {
            $action = $this->key_ . 'schedule_create';
            $url_back = $this->adminLink('options');

            include 'views/admin_schedule_create.php';
        } elseif($key == 'schedule-update' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'schedule_update';
            $url_back = $this->adminLink('options');

            $schedule = $cloneGuardSecurityAPI->getSchedule($subkey);

            $frequency = 'one_time';
            if($schedule['period_unit'] == 'day') {
                $frequency = 'daily';
            } elseif($schedule['period_unit'] == 'week') {
                $frequency = 'weekly';
            } elseif($schedule['period_unit'] == 'month') {
                $frequency = 'monthly';
            }

            include 'views/admin_schedule_edit.php';
        } elseif($key == 'target-create') {
            $action = $this->key_ . 'target_create';
            $url_back = $this->adminLink('options');

            include 'views/admin_target_create.php';
        } elseif($key == 'target-update' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'target_update';
            $url_back = $this->adminLink('options');

            $target = $cloneGuardSecurityAPI->getTarget($subkey);
            
            include 'views/admin_target_edit.php';
        } elseif($key == 'notification-create') {
            $action = $this->key_ . 'notification_create';
            $url_back = $this->adminLink('options');

            include 'views/admin_notification_create.php';
        } elseif($key == 'notification-update' && isset($_GET['subkey'])) {
            $action = $this->key_ . 'notification_update';
            $url_back = $this->adminLink('options');

            $notification = $cloneGuardSecurityAPI->getNotification($subkey);
            
            include 'views/admin_notification_edit.php';
        } else {
            $url_current = $this->adminLink('options');
            $nonce_schedule_delete = wp_create_nonce($this->key_ . 'schedule_delete');
            $nonce_target_delete = wp_create_nonce($this->key_ . 'target_delete');
            $nonce_notification_delete = wp_create_nonce($this->key_ . 'notification_delete');
            $nonce_update_app_type = wp_create_nonce($this->key_ . 'update_user_app_type');     

            if(isset($_GET['schedulesPaged'])) {
                $schedulesPaged = sanitize_text_field($_GET['schedulesPaged']);
                $schedules = $cloneGuardSecurityAPI->getAllSchedules($schedulesPaged);
            } elseif(isset($_GET['schedulesPaged']) == NULL) {
                $schedules = $cloneGuardSecurityAPI->getAllSchedules(1);
            }

            if(isset($_GET['targetsPaged'])) { 
                $targetsPaged = sanitize_text_field($_GET['targetsPaged']);
                $targets = $cloneGuardSecurityAPI->getAllTargets($targetsPaged);
            } elseif(isset($_GET['targetsPaged']) == NULL) {
                $targets = $cloneGuardSecurityAPI->getAllTargets(1);
            }
            
            if(isset($_GET['notificationsPaged'])) { 
                $notificationsPaged = sanitize_text_field($_GET['notificationsPaged']);
                $notifications = $cloneGuardSecurityAPI->getAllNotifications($notificationsPaged);
            } elseif(isset($_GET['notificationsPaged']) == NULL) {
                $notifications = $cloneGuardSecurityAPI->getAllNotifications(1);
            }

            $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];
            $this->userDetails['pciAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['pci'];
            $this->userDetails['vrmsAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['vrms'];
            $this->userDetails['penetrationAvailable'] = $cloneGuardSecurityAPI->getUserDetails()['organization']['available_products']['penetration'];  

            include 'views/admin_options.php';
        }
    }

    // Output the admin settings page.
    public function adminSettings() {
        global $cloneGuardSecurityAPI;

        $action = $this->key_ . 'settings';
        $title = 'CloneGuard Security Scanning';

        $portal_url = get_option($this->key_ . 'portal_url');

        if(!isset($portal_url) || $portal_url == '') {
            $portal_url = 'pciscan.clone-systems.com';
        } else {
            $portal_url = get_option($this->key_ . 'portal_url');
        }
        
        $user_token = get_option($this->key_ . 'user_token');  // 'cgss_user_token'
        $api_key = get_option($this->key_ . 'api_key');

        include 'views/admin_settings.php';
    }

    // AJAX to create a notification. 
    public function ajaxNotificationCreate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'notification_create')) {
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['scan_status']) || !in_array($_POST['scan_status'], ['Done', 'Running'])) {
                $pass = false;
                $output['messages'][] = 'Please enter a valid Scan Status.';
            }
            if(!isset($_POST['email_address']) || $_POST['email_address'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter an Email Address.';
            }

            if($pass) {
                $item = [];
                $item['notifications[name]'] = sanitize_text_field($_POST['name']);
                $item['notifications[to_address]'] = sanitize_text_field($_POST['email_address']);
                $item['notifications[status_changed]'] = sanitize_text_field($_POST['scan_status']);

                $success = $cloneGuardSecurityAPI->createNotification($item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully created.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to update a notification.
    public function ajaxNotificationUpdate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'notification_update')) {
            if(!isset($_POST['subkey']) || $_POST['subkey'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem with the ID.';
            }
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['status_changed']) || $_POST['status_changed'] == '') {
                $pass = false;
                $output['messages'][] = 'Please select a Scan Status.';
            }
            if(!isset($_POST['email_address']) || $_POST['email_address'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter an email address.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['subkey']);

                $item = [];
                $item['notifications[name]'] = sanitize_text_field($_POST['name']);
                $item['notifications[status_changed]'] = sanitize_text_field($_POST['status_changed']);
                $item['notifications[to_address]'] = sanitize_text_field($_POST['email_address']);

                $success = $cloneGuardSecurityAPI->updateNotification($id, $item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully updated.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to delete a notification.
    public function ajaxNotificationDelete() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'notification_delete')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->deleteNotification($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully deleted.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to delete a report. 
    public function ajaxReportDelete() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'report_delete')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->deleteReport($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully deleted.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to download a report. 
    public function ajaxReportDownload() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_GET['_wpnonce'], $this->key_ . 'report_download')) {
            if(!isset($_GET['id']) || $_GET['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }
            if(!isset($_GET['type']) || $_GET['type'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the Type.';
            }

            if($pass) {
                $id = sanitize_text_field($_GET['id']);
                $type = sanitize_text_field($_GET['type']);

                if($type == 'feedback') {
                    wp_redirect($this->feedback_url);
                    exit;
                } else {
                    $cloneGuardSecurityAPI->downloadReport($id, $type);
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to generate a report. 
    public function ajaxReportGenerate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        // Use the nonce for download
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'report_download')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }
            if(!isset($_POST['type']) || $_POST['type'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the Type.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);
                $type = sanitize_text_field($_POST['type']);

                $success = $cloneGuardSecurityAPI->generateReport($id, $type);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items are being generated.'];
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to check the status of a report that is being generated. 
    public function ajaxReportGenerateCheck() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        // Use the nonce for download
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'report_download')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }
            if(!isset($_POST['type']) || $_POST['type'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the Type.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);
                $type = sanitize_text_field($_POST['type']);

                $success = $cloneGuardSecurityAPI->generateReportCheck($id, $type);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The item has been generated.'];
                } else {
                    $output['status'] = 'success';  
                    $output['still_processing'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to create a scan.
    public function ajaxScanCreate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;

        $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];

        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'scan_create')) {
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['target']) || $_POST['target'] == '') {
                $pass = false;
                $output['messages'][] = 'Please select a Target.';
            }
            if ($app_type == 'vrms' || $app_type == 'penetration') {
                if(!isset($_POST['scanner']) || $_POST['scanner'] == '') {
                    $pass = false;
                    $output['messages'][] = 'Please select a Scanner profile.';
                }
                if(!isset($_POST['scan_config']) || $_POST['scan_config'] == '') {
                    $pass = false;
                    $output['messages'][] = 'Please select a Config.';
                }
            }

            if($pass) {
                $item = [];
                $item['scans[name]'] = sanitize_text_field($_POST['name']);
                $item['scans[schedule_id]'] = sanitize_text_field($_POST['schedule']);
                $item['scans[target_id]'] = sanitize_text_field($_POST['target']);
                $item['scans[notification_ids]'] = sanitize_text_field($_POST['notifications']);
                if ($app_type == 'vrms' || $app_type == 'penetration') {
                    $item['scans[config_id]'] = sanitize_text_field($_POST['scan_config']);
                    $item['scans[scanner_id]'] = sanitize_text_field($_POST['scanner']);
                }
                $item['scans[comment]'] = sanitize_textarea_field($_POST['comment']);

                $success = $cloneGuardSecurityAPI->createScan($item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully created.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to update a scan.
    public function ajaxScanUpdate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;

        $app_type = $cloneGuardSecurityAPI->getUserDetails()['app_type'];

        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'scan_update')) {
            if(!isset($_POST['key']) || $_POST['key'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem with the ID.';
            }
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['target']) || $_POST['target'] == '') {
                $pass = false;
                $output['messages'][] = 'Please select a Target.';
            }
            if ($app_type == 'vrms' || $app_type == 'penetration') {
                if(!isset($_POST['scanner']) || $_POST['scanner'] == '') {
                    $pass = false;
                    $output['messages'][] = 'Please select a Scanner profile.';
                }
                if(!isset($_POST['scan_config']) || $_POST['scan_config'] == '') {
                    $pass = false;
                    $output['messages'][] = 'Please select a Config.';
                }
            }

            if($pass) {
                $id = sanitize_text_field($_POST['key']);

                $notification_ids = '';
                if(isset($_POST['notifications']) && is_array($_POST['notifications'])) {
                    foreach($_POST['notifications'] as $notification) {
                        if($notification_ids) {
                            $notification_ids .= ',' . $notification;
                        } else {
                            $notification_ids .= $notification;
                        }
                    }
                }

                $item = [];
                $item['scans[name]'] = sanitize_text_field($_POST['name']);
                $item['scans[schedule_id]'] = sanitize_text_field($_POST['schedule']);
                $item['scans[target_id]'] = sanitize_text_field($_POST['target']);
                $item['scans[notification_ids]'] = $notification_ids;
                if ($app_type == 'vrms' || $app_type == 'penetration') {
                    $item['scans[config_id]'] = sanitize_text_field($_POST['scan_config']);
                    $item['scans[scanner_id]'] = sanitize_text_field($_POST['scanner']);
                }
                $item['scans[comment]'] = sanitize_textarea_field($_POST['comment']);

                $success = $cloneGuardSecurityAPI->updateScan($id, $item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully updated.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to delete a scan.
    public function ajaxScanDelete() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'scan_delete')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->deleteScan($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully deleted.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to start a scheduled scan.
    public function ajaxScanScheduledStart() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'scan_action')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->startScanScheduled($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully started.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to reload the scans on a page.
    // Used to keep the scans list page updated.
    public function ajaxScansReload() {
        if(current_user_can('manage_options')) {
            $this->adminScans();
        }
        exit;
    }

    // AJAX to start a scan.
    public function ajaxScanStart() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'scan_action')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->startScan($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully started.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to stop a scan.
    public function ajaxScanStop() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'scan_action')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->stopScan($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully stopped.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to temporarily save the scan data.
    // Used when navigating to a subpage.
    public function ajaxScanTempSave() {
        $output = [];
        $output['status'] = 'error';
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'scan_temp_save')) {
            if(isset($_POST['key'])) {
                $key = sanitize_text_field($_POST['key']);
            } else {
                $key = '';
                $pass = false;
            }

            if($pass) {
                $user_id = get_current_user_id();

                $scan = [];
                $scan['name'] = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
                $scan['schedule'] = isset($_POST['schedule']) ? sanitize_text_field($_POST['schedule']) : '';
                $scan['target'] = isset($_POST['target']) ? sanitize_text_field($_POST['target']) : '';
                $scan['notifications'] = isset($_POST['notifications']) ? sanitize_text_field($_POST['notifications']) : '';
                if ($app_type == 'vrms' || $app_type == 'penetration') {
                    $scan['scanner'] = isset($_POST['scanner']) ? sanitize_textarea_field($_POST['scanner']) : '';
                    $scan['config'] = isset($_POST['scan_config']) ? sanitize_textarea_field($_POST['scan_config']) : '';
                }
                $scan['comment'] = isset($_POST['comment']) ? sanitize_textarea_field($_POST['comment']) : '';

                $scans = get_user_meta($user_id, $this->key_ . 'scans_temp_save', true);

                if(!is_array($scans)) {
                    $scans = [];
                }
                $scans[$key] = $scan;

                update_user_meta($user_id, $this->key_ . 'scans_temp_save', $scans);

                $output['status'] = 'success';  
                $output['messages'] = ['The items have been successfully updated.'];
            }
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to create a schedule.
    public function ajaxScheduleCreate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'schedule_create')) {
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['frequency']) || $_POST['frequency'] == '') {
                $pass = false;
                $output['messages'][] = 'Please select a Frequency.';
            }
            if(!isset($_POST['first_time']) || $_POST['first_time'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a First Time.';
            }
            if(!isset($_POST['timezone']) || $_POST['timezone'] == '') {
                $pass = false;
                $output['messages'][] = 'Please select a Timezone.';
            }

            if($pass) {
                $frequency = sanitize_text_field($_POST['frequency']);

                $item = [];
                $item['schedules[name]'] = sanitize_text_field($_POST['name']);
                $item['schedules[first_time]'] = sanitize_text_field($_POST['first_time']);
                $item['schedules[timezone]'] = sanitize_text_field($_POST['timezone']);
                if($frequency == 'one_time') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'once';
                } elseif($frequency == 'daily') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'day';
                } elseif($frequency == 'weekly') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'week';
                } elseif($frequency == 'monthly') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'month';
                }
                $item['schedules[comment]'] = sanitize_textarea_field($_POST['comment']);

                $success = $cloneGuardSecurityAPI->createSchedule($item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully created.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to update a schedule.
    public function ajaxScheduleUpdate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'schedule_update')) {
            if(!isset($_POST['subkey']) || $_POST['subkey'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem with the ID.';
            }
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['frequency']) || $_POST['frequency'] == '') {
                $pass = false;
                $output['messages'][] = 'Please select a Frequency.';
            }
            if(!isset($_POST['first_time']) || $_POST['first_time'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a First Time.';
            }
            if(!isset($_POST['timezone']) || $_POST['timezone'] == '') {
                $pass = false;
                $output['messages'][] = 'Please select a Timezone.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['subkey']);

                $frequency = sanitize_text_field($_POST['frequency']);

                $item = [];
                $item['schedules[name]'] = sanitize_text_field($_POST['name']);
                $item['schedules[first_time]'] = sanitize_text_field($_POST['first_time']);
                $item['schedules[timezone]'] = sanitize_text_field($_POST['timezone']);
                if($frequency == 'one_time') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'once';
                } elseif($frequency == 'daily') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'day';
                } elseif($frequency == 'weekly') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'week';
                } elseif($frequency == 'monthly') {
                    $item['schedules[period]'] = sanitize_text_field($_POST['period']);
                    $item['schedules[period_unit]'] = 'month';
                }
                $item['schedules[comment]'] = sanitize_textarea_field($_POST['comment']);

                $success = $cloneGuardSecurityAPI->updateSchedule($id, $item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully updated.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to delete a schedule.
    public function ajaxScheduleDelete() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'schedule_delete')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->deleteSchedule($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully deleted.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to save settings.
    public function ajaxSettings() {
        $output = [];
        $output['status'] = 'error';
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'settings')) {
            if(!isset($_POST['user_token']) || $_POST['user_token'] == '') {
                $pass = false;
                $output['messages'] = ['Please enter your User Token.'];
            }
            if(!isset($_POST['api_key']) || $_POST['api_key'] == '') {
                $pass = false;
                $output['messages'] = ['Please enter your API Key.'];
            }

            if($pass) {
                if(!isset($_POST['portal_url']) || $_POST['portal_url'] == '') {
                    $portal_url = 'pciscan.clone-systems.com';
                } else {
                    $portal_url = sanitize_text_field($_POST['portal_url']);
                }
                $user_token = sanitize_text_field($_POST['user_token']);
                $api_key = sanitize_text_field($_POST['api_key']);

                update_option($this->key_ . 'portal_url', $portal_url);
                update_option($this->key_ . 'user_token', $user_token);
                update_option($this->key_ . 'api_key', $api_key);

                $output['status'] = 'success';  
                $output['messages'] = ['The items have been successfully updated.'];
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to create a target.
    public function ajaxTargetCreate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'target_create')) {
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['hosts']) || $_POST['hosts'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a list of Hosts.';
            }
            if(!isset($_POST['attest']) || $_POST['attest'] != 'yes') {
                $pass = false;
                $output['messages'][] = 'Please agree to the terms.';
            }

            if($pass) {
                $item = [];
                $item['targets[name]'] = sanitize_text_field($_POST['name']);
                $item['targets[hosts]'] = sanitize_textarea_field($_POST['hosts']);
                $item['targets[comment]'] = sanitize_textarea_field($_POST['comment']);

                $success = $cloneGuardSecurityAPI->createTarget($item)['status'];
                // TODO
                // $target_id = $cloneGuardSecurityAPI->createTarget($item)['id'];

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully created.'];
                    $output['redirect'] = true;
                    $output['targetId'] = $target_id;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to update a target.
    public function ajaxTargetUpdate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'target_update')) {
            if(!isset($_POST['subkey']) || $_POST['subkey'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem with the ID.';
            }
            if(!isset($_POST['name']) || $_POST['name'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Name.';
            }
            if(!isset($_POST['hosts']) || $_POST['hosts'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter the hosts.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['subkey']);

                $item = [];
                $item['targets[name]'] = sanitize_text_field($_POST['name']);
                $item['targets[hosts]'] = sanitize_textarea_field($_POST['hosts']);
                $item['targets[comment]'] = sanitize_textarea_field($_POST['comment']);

                $success = $cloneGuardSecurityAPI->updateTarget($id, $item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully updated.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to delete a target.
    public function ajaxTargetDelete() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'target_delete')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                $success = $cloneGuardSecurityAPI->deleteTarget($id);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully deleted.'];
                    $output['reload'] = true;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // Ensures the API settings have been saved. Otherwise, redirects user to settings page.
    // Also handles any bulk actions on the scans and reports pages.
    public function adminTemplateRedirect() {
        global $pagenow, $cloneGuardSecurityAPI;

        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == $this->key_ . 'overview') {
            $this->user_token = get_option($this->key_ . 'user_token');
            $this->api_key = get_option($this->key_ . 'api_key');

            // Make sure the API keys are valid.
            if(
                !isset($this->user_token) 
                || empty($this->user_token)
                || !isset($this->api_key) 
                || empty($this->api_key)
            ) {
                wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                exit;
            } else {
                $scans = $cloneGuardSecurityAPI->getScans(1);
                if($scans === false) {
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                    exit;
                }
            }
        }

        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == $this->key_ . 'scans') {
            $this->user_token = get_option($this->key_ . 'user_token');
            $this->api_key = get_option($this->key_ . 'api_key');

            // Make sure the API keys are valid.
            if(
                !isset($this->user_token) 
                || empty($this->user_token)
                || !isset($this->api_key) 
                || empty($this->api_key)
            ) {
                wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                exit;
            } else {
                $scans = $cloneGuardSecurityAPI->getScans(1);
                if($scans === false) {
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                    exit;
                }
            }

            // Handle scan deletes.
            if(
                (isset($_GET['action']) && $_GET['action'] == 'delete')
                || (isset($_GET['action2']) && $_GET['action2'] == 'delete')
            ) {
                if(
                    isset($_GET['_wpnonce'])
                    && wp_verify_nonce($_GET['_wpnonce'], $this->key_ . 'scans')
                    && isset($_GET['scans']) 
                    && is_array($_GET['scans'])
                ) {
                    // $_GET['scans'] must be an array to reach this point.
                    // Sanitized below.
                    $scans = $_GET['scans'];

                    // Sanitize each element in array.
                    foreach($scans as $key => $scan) {
                        $scans[$key] = sanitize_text_field($scan);
                    }

                    foreach($scans as $scan) {
                        $success = $cloneGuardSecurityAPI->deleteScan($scan);
                    }
                    $paged = 1;
                    if(isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                        $paged = $_GET['paged'];
                    }
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'scans' . '&paged=' . $paged));
                    exit;
                }
            }
        }

        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == $this->key_ . 'vulnerabilities') {
            $this->user_token = get_option($this->key_ . 'user_token');
            $this->api_key = get_option($this->key_ . 'api_key');

            // Make sure the API keys are valid.
            if(
                !isset($this->user_token) 
                || empty($this->user_token)
                || !isset($this->api_key) 
                || empty($this->api_key)
            ) {
                wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                exit;
            } else {
                $vulnerabilities = $cloneGuardSecurityAPI->getResults(1);
                if($vulnerabilities === false) {
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                    exit;
                }
            }

            // Handle vulnerabilities exceptions.
            if((isset($_GET['action']) && $_GET['action'] == 'add-exception') || (isset($_GET['action2']) && $_GET['action2'] == 'add-exception')) 
            {
                if(isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], $this->key_ . 'vulnerabilities') && isset($_GET['vulnerabilities']) && is_array($_GET['vulnerabilities'])) {

                    $vulnerabilitiesIds = $_GET['vulnerabilities'];

                    foreach($vulnerabilitiesIds as $key => $vul) {
                        $current_vul = $cloneGuardSecurityAPI->getResult($vul);
                        $item = [];
                        $item['exceptions[text]'] = $_GET['modal_justification_text'];
                        $item['exceptions[nvt]'] = sanitize_text_field($current_vul['nvt']['oid']);
                        $item['exceptions[task]'] = sanitize_text_field($current_vul['task']['id']);
                        $cloneGuardSecurityAPI->createException($item);
                    }

                    $paged = 1;
                    if(isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                        $paged = $_GET['paged'];
                    }
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'vulnerabilities' . '&paged=' . $paged));
                    exit;
                }
            }

        }

        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == $this->key_ . 'reports') {
            $this->user_token = get_option($this->key_ . 'user_token');
            $this->api_key = get_option($this->key_ . 'api_key');

            // Make sure the API keys are valid.
            if(
                !isset($this->user_token) 
                || empty($this->user_token)
                || !isset($this->api_key) 
                || empty($this->api_key)
            ) {
                wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                exit;
            } else {
                $reports = $cloneGuardSecurityAPI->getReports(1);
                if($reports === false) {
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                    exit;
                }
            }

            // Handle report deletes.
            if(
                (isset($_GET['action']) && $_GET['action'] == 'delete')
                || (isset($_GET['action2']) && $_GET['action2'] == 'delete')
            ) {
                if(
                    isset($_GET['_wpnonce'])
                    && wp_verify_nonce($_GET['_wpnonce'], $this->key_ . 'reports')
                    && isset($_GET['reports']) 
                    && is_array($_GET['reports'])
                ) {
                    // $_GET['reports'] must be an array to reach this point.
                    // Sanitized below.
                    $reports = $_GET['reports'];

                    // Sanitize each element in array.
                    foreach($reports as $key => $report) {
                        $reports[$key] = sanitize_text_field($report);
                    }

                    foreach($reports as $report) {
                        $success = $cloneGuardSecurityAPI->deleteReport($report);
                    }
                    $paged = 1;
                    if(isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                        $paged = $_GET['paged'];
                    }
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'reports' . '&paged=' . $paged));
                    exit;
                }
            }
        }

        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == $this->key_ . 'options') {
            $this->user_token = get_option($this->key_ . 'user_token');
            $this->api_key = get_option($this->key_ . 'api_key');

            // Make sure the API keys are valid.
            if(!isset($this->user_token) || empty($this->user_token) || !isset($this->api_key) || empty($this->api_key)) {
                wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                exit;
            } else {
                $schedules = $cloneGuardSecurityAPI->getAllSchedules(1);
                $targets = $cloneGuardSecurityAPI->getAllTargets(1);
                $notifications = $cloneGuardSecurityAPI->getAllNotifications(1);

                if($schedules === false || $targets === false || $notifications === false) {
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'settings' . '&msg=access'));
                    exit;
                }
            }

            // Handle Schedules, Targets, Notifications deletes.
            if(  (isset($_GET['action']) && $_GET['action'] == 'delete') ||
                 (isset($_GET['action2']) && $_GET['action2'] == 'delete') || 
                 (isset($_GET['action3']) && $_GET['action3'] == 'delete') ||
                 (isset($_GET['action4']) && $_GET['action4'] == 'delete')) {
                if(isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], $this->key_ . 'options') &&
                   isset($_GET['schedules']) && is_array($_GET['schedules']))
                {
                    $schedules = $_GET['schedules'];

                    foreach($schedules as $key => $schedule) {
                        $schedules[$key] = sanitize_text_field($schedule);
                    }

                    foreach($schedules as $schedule) {
                        $success = $cloneGuardSecurityAPI->deleteSchedule($schedule);
                    }
                    wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'options'));
                    exit;
                } elseif(isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], $this->key_ . 'options') &&
                         isset($_GET['targets']) && is_array($_GET['targets'])) 
                {
                   $targets = $_GET['targets'];

                   foreach($targets as $key => $target) {
                       $targets[$key] = sanitize_text_field($target);
                   }

                   foreach($targets as $target) {
                       $success = $cloneGuardSecurityAPI->deleteTarget($target);
                   }
                   wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'options'));
                   exit;
                } elseif(isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], $this->key_ . 'options') &&
                        isset($_GET['notifications']) && is_array($_GET['notifications'])) 
                {
                  $notifications = $_GET['notifications'];

                  foreach($notifications as $key => $notification) {
                      $notifications[$key] = sanitize_text_field($notification);
                  }

                  foreach($notifications as $notification) {
                      $success = $cloneGuardSecurityAPI->deleteNotification($notification);
                  }
                  wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'options'));
                  exit;
                }
            }
        }
    }

    function updateUserAppType() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'update_user_app_type')) {
            if(!isset($_POST['apptype']) || $_POST['apptype'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the app type.';
            }

            if($pass) {
                $theAppType = sanitize_text_field($_POST['apptype']);

                $success = $cloneGuardSecurityAPI->updateUserAppType($theAppType);

                if($success) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully deleted.'];
                    $output['reload'] = true;
                    $output['appType'] = $theAppType;
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to create an exception.
    public function ajaxExceptionCreate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;

        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'exception_create')) {
            if(!isset($_POST['justification_text']) || $_POST['justification_text'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Justification.';
            }
            if(!isset($_POST['nvt_oid']) || $_POST['nvt_oid'] == '') {
                $pass = false;
                $output['messages'][] = 'NVT oid is missing.';
            }
            if(!isset($_POST['scanOrHost']) || $_POST['scanOrHost'] == '') {
                $pass = false;
                $output['messages'][] = 'Scan or Host radio button has no value.';
            }
            if ($_POST['scanOrHost'] == 'scan') {
                if(!isset($_POST['task_id']) || $_POST['task_id'] == '') {
                    $pass = false;
                    $output['messages'][] = 'Task id is missing';
                }
            }
            if ($_POST['scanOrHost'] == 'host') {
                if(!isset($_POST['host']) || $_POST['host'] == '') {
                    $pass = false;
                    $output['messages'][] = 'Host IP address is missing.';
                }
            }

            if($pass) {
                $item = [];
                $item['exceptions[text]'] = sanitize_text_field($_POST['justification_text']);
                $item['exceptions[nvt]'] = sanitize_text_field($_POST['nvt_oid']);

                if ($_POST['scanOrHost'] == 'scan') {
                    $item['exceptions[task]'] = sanitize_text_field($_POST['task_id']);
                } 
                elseif ($_POST['scanOrHost'] == 'host') {
                    $item['exceptions[hosts]'] = sanitize_text_field($_POST['host']);
                }

                $success = $cloneGuardSecurityAPI->createException($item);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully created.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    $output['messages'] = [$success];
                }
            }
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // AJAX to update an exception.
    public function ajaxExceptionUpdate() {
        global $cloneGuardSecurityAPI;
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'exception_update')) {
            if(!isset($_POST['justification_text_edit']) || $_POST['justification_text_edit'] == '') {
                $pass = false;
                $output['messages'][] = 'Please enter a Justification.';
            }
            if(!isset($_POST['nvt_oid']) || $_POST['nvt_oid'] == '') {
                $pass = false;
                $output['messages'][] = 'NVT oid is missing.';
            }


            if($pass) {
                $id = sanitize_text_field($_POST['exception_id']);

                $text = sanitize_text_field($_POST['justification_text_edit']);
                $nvt_oid = sanitize_text_field($_POST['nvt_oid']);

                $success = $cloneGuardSecurityAPI->updateException($id, $text, $nvt_oid);

                if($success === true) {
                    $output['status'] = 'success';  
                    $output['messages'] = ['The items have been successfully updated.'];
                    $output['redirect'] = true;
                } elseif($success) {
                    // Error message returned.
                    $output['messages'] = [$success];
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // Initialize all the hooks and filters.
    public function init() {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('admin_menu', [$this, 'adminMenu']);

        add_action('wp_ajax_' . $this->key_ . 'settings', [$this, 'ajaxSettings']);
        add_action('wp_ajax_' . $this->key_ . 'scan_temp_save', [$this, 'ajaxScanTempSave']);

        add_action('wp_ajax_' . $this->key_ . 'report_delete', [$this, 'ajaxReportDelete']);
        add_action('wp_ajax_' . $this->key_ . 'report_download', [$this, 'ajaxReportDownload']);
        add_action('wp_ajax_' . $this->key_ . 'report_generate', [$this, 'ajaxReportGenerate']);
        add_action('wp_ajax_' . $this->key_ . 'report_generate_check', [$this, 'ajaxReportGenerateCheck']);
        
        add_action('wp_ajax_' . $this->key_ . 'scan_create', [$this, 'ajaxScanCreate']);
        add_action('wp_ajax_' . $this->key_ . 'scan_delete', [$this, 'ajaxScanDelete']);
        add_action('wp_ajax_' . $this->key_ . 'scan_start', [$this, 'ajaxScanStart']);
        add_action('wp_ajax_' . $this->key_ . 'scan_scheduled_start', [$this, 'ajaxScanScheduledStart']);
        add_action('wp_ajax_' . $this->key_ . 'scan_stop', [$this, 'ajaxScanStop']);
        add_action('wp_ajax_' . $this->key_ . 'scan_update', [$this, 'ajaxScanUpdate']);

        add_action('wp_ajax_' . $this->key_ . 'scans_reload', [$this, 'ajaxScansReload']);

        add_action('wp_ajax_' . $this->key_ . 'schedule_create', [$this, 'ajaxScheduleCreate']);
        add_action('wp_ajax_' . $this->key_ . 'schedule_update', [$this, 'ajaxScheduleUpdate']);
        add_action('wp_ajax_' . $this->key_ . 'schedule_delete', [$this, 'ajaxScheduleDelete']);


        add_action('wp_ajax_' . $this->key_ . 'target_create', [$this, 'ajaxTargetCreate']);
        add_action('wp_ajax_' . $this->key_ . 'target_update', [$this, 'ajaxTargetUpdate']);
        add_action('wp_ajax_' . $this->key_ . 'target_delete', [$this, 'ajaxTargetDelete']);


        add_action('wp_ajax_' . $this->key_ . 'notification_create', [$this, 'ajaxNotificationCreate']);
        add_action('wp_ajax_' . $this->key_ . 'notification_update', [$this, 'ajaxNotificationUpdate']);
        add_action('wp_ajax_' . $this->key_ . 'notification_delete', [$this, 'ajaxNotificationDelete']);

        add_action('wp_ajax_' . $this->key_ . 'update_user_app_type', [$this, 'updateUserAppType']);

        add_action('wp_ajax_' . $this->key_ . 'exception_create', [$this, 'ajaxExceptionCreate']);
        add_action('wp_ajax_' . $this->key_ . 'exception_update', [$this, 'ajaxExceptionUpdate']);


        add_action('admin_init', [$this, 'adminTemplateRedirect']);
    }

    // Output the quarter of the passed in date.
    // Q4 2020
    public function getQuarter($date) {
        $time = strtotime($date);

        $output = '';
        $months = [];
        $months['January'] = 'Q1';
        $months['February'] = 'Q1';
        $months['March'] = 'Q1';
        $months['April'] = 'Q2';
        $months['May'] = 'Q2';
        $months['June'] = 'Q2';
        $months['July'] = 'Q3';
        $months['August'] = 'Q3';
        $months['September'] = 'Q3';
        $months['October'] = 'Q4';
        $months['November'] = 'Q4';
        $months['December'] = 'Q4';
        $output = $months[date('F', $time)];
        $output .= ' ' . date('Y', $time);

        return $output;
    }

    // Returns a list of timezones.
    public function getTimezones() {
        $timezones = [];
        $timezones[] = 'Africa/Abidjan';
        $timezones[] = 'Africa/Accra';
        $timezones[] = 'Africa/Addis_Ababa';
        $timezones[] = 'Africa/Algiers';
        $timezones[] = 'Africa/Asmara';
        $timezones[] = 'Africa/Asmera';
        $timezones[] = 'Africa/Bamako';
        $timezones[] = 'Africa/Bangui';
        $timezones[] = 'Africa/Banjul';
        $timezones[] = 'Africa/Bissau';
        $timezones[] = 'Africa/Blantyre';
        $timezones[] = 'Africa/Brazzaville';
        $timezones[] = 'Africa/Bujumbura';
        $timezones[] = 'Africa/Cairo';
        $timezones[] = 'Africa/Casablanca';
        $timezones[] = 'Africa/Ceuta';
        $timezones[] = 'Africa/Conakry';
        $timezones[] = 'Africa/Dakar';
        $timezones[] = 'Africa/Dar_es_Salaam';
        $timezones[] = 'Africa/Djibouti';
        $timezones[] = 'Africa/Douala';
        $timezones[] = 'Africa/El_Aaiun';
        $timezones[] = 'Africa/Freetown';
        $timezones[] = 'Africa/Gaborone';
        $timezones[] = 'Africa/Harare';
        $timezones[] = 'Africa/Johannesburg';
        $timezones[] = 'Africa/Juba';
        $timezones[] = 'Africa/Kampala';
        $timezones[] = 'Africa/Khartoum';
        $timezones[] = 'Africa/Kigali';
        $timezones[] = 'Africa/Kinshasa';
        $timezones[] = 'Africa/Lagos';
        $timezones[] = 'Africa/Libreville';
        $timezones[] = 'Africa/Lome';
        $timezones[] = 'Africa/Luanda';
        $timezones[] = 'Africa/Lubumbashi';
        $timezones[] = 'Africa/Lusaka';
        $timezones[] = 'Africa/Malabo';
        $timezones[] = 'Africa/Maputo';
        $timezones[] = 'Africa/Maseru';
        $timezones[] = 'Africa/Mbabane';
        $timezones[] = 'Africa/Mogadishu';
        $timezones[] = 'Africa/Monrovia';
        $timezones[] = 'Africa/Nairobi';
        $timezones[] = 'Africa/Ndjamena';
        $timezones[] = 'Africa/Niamey';
        $timezones[] = 'Africa/Nouakchott';
        $timezones[] = 'Africa/Ouagadougou';
        $timezones[] = 'Africa/Porto-Novo';
        $timezones[] = 'Africa/Sao_Tome';
        $timezones[] = 'Africa/Timbuktu';
        $timezones[] = 'Africa/Tripoli';
        $timezones[] = 'Africa/Tunis';
        $timezones[] = 'Africa/Windhoek';
        $timezones[] = 'America/Adak';
        $timezones[] = 'America/Anchorage';
        $timezones[] = 'America/Anguilla';
        $timezones[] = 'America/Antigua';
        $timezones[] = 'America/Araguaina';
        $timezones[] = 'America/Argentina/Buenos_Aires';
        $timezones[] = 'America/Argentina/Catamarca';
        $timezones[] = 'America/Argentina/ComodRivadavia';
        $timezones[] = 'America/Argentina/Cordoba';
        $timezones[] = 'America/Argentina/Jujuy';
        $timezones[] = 'America/Argentina/La_Rioja';
        $timezones[] = 'America/Argentina/Mendoza';
        $timezones[] = 'America/Argentina/Rio_Gallegos';
        $timezones[] = 'America/Argentina/Salta';
        $timezones[] = 'America/Argentina/San_Juan';
        $timezones[] = 'America/Argentina/San_Luis';
        $timezones[] = 'America/Argentina/Tucuman';
        $timezones[] = 'America/Argentina/Ushuaia';
        $timezones[] = 'America/Aruba';
        $timezones[] = 'America/Asuncion';
        $timezones[] = 'America/Atikokan';
        $timezones[] = 'America/Atka';
        $timezones[] = 'America/Bahia';
        $timezones[] = 'America/Bahia_Banderas';
        $timezones[] = 'America/Barbados';
        $timezones[] = 'America/Belem';
        $timezones[] = 'America/Belize';
        $timezones[] = 'America/Blanc-Sablon';
        $timezones[] = 'America/Boa_Vista';
        $timezones[] = 'America/Bogota';
        $timezones[] = 'America/Boise';
        $timezones[] = 'America/Buenos_Aires';
        $timezones[] = 'America/Cambridge_Bay';
        $timezones[] = 'America/Campo_Grande';
        $timezones[] = 'America/Cancun';
        $timezones[] = 'America/Caracas';
        $timezones[] = 'America/Catamarca';
        $timezones[] = 'America/Cayenne';
        $timezones[] = 'America/Cayman';
        $timezones[] = 'America/Chicago';
        $timezones[] = 'America/Chihuahua';
        $timezones[] = 'America/Coral_Harbour';
        $timezones[] = 'America/Cordoba';
        $timezones[] = 'America/Costa_Rica';
        $timezones[] = 'America/Creston';
        $timezones[] = 'America/Cuiaba';
        $timezones[] = 'America/Curacao';
        $timezones[] = 'America/Danmarkshavn';
        $timezones[] = 'America/Dawson';
        $timezones[] = 'America/Dawson_Creek';
        $timezones[] = 'America/Denver';
        $timezones[] = 'America/Detroit';
        $timezones[] = 'America/Dominica';
        $timezones[] = 'America/Edmonton';
        $timezones[] = 'America/Eirunepe';
        $timezones[] = 'America/El_Salvador';
        $timezones[] = 'America/Ensenada';
        $timezones[] = 'America/Fort_Nelson';
        $timezones[] = 'America/Fort_Wayne';
        $timezones[] = 'America/Fortaleza';
        $timezones[] = 'America/Glace_Bay';
        $timezones[] = 'America/Godthab';
        $timezones[] = 'America/Goose_Bay';
        $timezones[] = 'America/Grand_Turk';
        $timezones[] = 'America/Grenada';
        $timezones[] = 'America/Guadeloupe';
        $timezones[] = 'America/Guatemala';
        $timezones[] = 'America/Guayaquil';
        $timezones[] = 'America/Guyana';
        $timezones[] = 'America/Halifax';
        $timezones[] = 'America/Havana';
        $timezones[] = 'America/Hermosillo';
        $timezones[] = 'America/Indiana/Indianapolis';
        $timezones[] = 'America/Indiana/Knox';
        $timezones[] = 'America/Indiana/Marengo';
        $timezones[] = 'America/Indiana/Petersburg';
        $timezones[] = 'America/Indiana/Tell_City';
        $timezones[] = 'America/Indiana/Vevay';
        $timezones[] = 'America/Indiana/Vincennes';
        $timezones[] = 'America/Indiana/Winamac';
        $timezones[] = 'America/Indianapolis';
        $timezones[] = 'America/Inuvik';
        $timezones[] = 'America/Iqaluit';
        $timezones[] = 'America/Jamaica';
        $timezones[] = 'America/Jujuy';
        $timezones[] = 'America/Juneau';
        $timezones[] = 'America/Kentucky/Louisville';
        $timezones[] = 'America/Kentucky/Monticello';
        $timezones[] = 'America/Knox_IN';
        $timezones[] = 'America/Kralendijk';
        $timezones[] = 'America/La_Paz';
        $timezones[] = 'America/Lima';
        $timezones[] = 'America/Los_Angeles';
        $timezones[] = 'America/Louisville';
        $timezones[] = 'America/Lower_Princes';
        $timezones[] = 'America/Maceio';
        $timezones[] = 'America/Managua';
        $timezones[] = 'America/Manaus';
        $timezones[] = 'America/Marigot';
        $timezones[] = 'America/Martinique';
        $timezones[] = 'America/Matamoros';
        $timezones[] = 'America/Mazatlan';
        $timezones[] = 'America/Mendoza';
        $timezones[] = 'America/Menominee';
        $timezones[] = 'America/Merida';
        $timezones[] = 'America/Metlakatla';
        $timezones[] = 'America/Mexico_City';
        $timezones[] = 'America/Miquelon';
        $timezones[] = 'America/Moncton';
        $timezones[] = 'America/Monterrey';
        $timezones[] = 'America/Montevideo';
        $timezones[] = 'America/Montreal';
        $timezones[] = 'America/Montserrat';
        $timezones[] = 'America/Nassau';
        $timezones[] = 'America/New_York';
        $timezones[] = 'America/Nipigon';
        $timezones[] = 'America/Nome';
        $timezones[] = 'America/Noronha';
        $timezones[] = 'America/North_Dakota/Beulah';
        $timezones[] = 'America/North_Dakota/Center';
        $timezones[] = 'America/North_Dakota/New_Salem';
        $timezones[] = 'America/Nuuk';
        $timezones[] = 'America/Ojinaga';
        $timezones[] = 'America/Panama';
        $timezones[] = 'America/Pangnirtung';
        $timezones[] = 'America/Paramaribo';
        $timezones[] = 'America/Phoenix';
        $timezones[] = 'America/Port-au-Prince';
        $timezones[] = 'America/Port_of_Spain';
        $timezones[] = 'America/Porto_Acre';
        $timezones[] = 'America/Porto_Velho';
        $timezones[] = 'America/Puerto_Rico';
        $timezones[] = 'America/Punta_Arenas';
        $timezones[] = 'America/Rainy_River';
        $timezones[] = 'America/Rankin_Inlet';
        $timezones[] = 'America/Recife';
        $timezones[] = 'America/Regina';
        $timezones[] = 'America/Resolute';
        $timezones[] = 'America/Rio_Branco';
        $timezones[] = 'America/Rosario';
        $timezones[] = 'America/Santa_Isabel';
        $timezones[] = 'America/Santarem';
        $timezones[] = 'America/Santiago';
        $timezones[] = 'America/Santo_Domingo';
        $timezones[] = 'America/Sao_Paulo';
        $timezones[] = 'America/Scoresbysund';
        $timezones[] = 'America/Shiprock';
        $timezones[] = 'America/Sitka';
        $timezones[] = 'America/St_Barthelemy';
        $timezones[] = 'America/St_Johns';
        $timezones[] = 'America/St_Kitts';
        $timezones[] = 'America/St_Lucia';
        $timezones[] = 'America/St_Thomas';
        $timezones[] = 'America/St_Vincent';
        $timezones[] = 'America/Swift_Current';
        $timezones[] = 'America/Tegucigalpa';
        $timezones[] = 'America/Thule';
        $timezones[] = 'America/Thunder_Bay';
        $timezones[] = 'America/Tijuana';
        $timezones[] = 'America/Toronto';
        $timezones[] = 'America/Tortola';
        $timezones[] = 'America/Vancouver';
        $timezones[] = 'America/Virgin';
        $timezones[] = 'America/Whitehorse';
        $timezones[] = 'America/Winnipeg';
        $timezones[] = 'America/Yakutat';
        $timezones[] = 'America/Yellowknife';
        $timezones[] = 'Antarctica/Casey';
        $timezones[] = 'Antarctica/Davis';
        $timezones[] = 'Antarctica/DumontDUrville';
        $timezones[] = 'Antarctica/Macquarie';
        $timezones[] = 'Antarctica/Mawson';
        $timezones[] = 'Antarctica/McMurdo';
        $timezones[] = 'Antarctica/Palmer';
        $timezones[] = 'Antarctica/Rothera';
        $timezones[] = 'Antarctica/South_Pole';
        $timezones[] = 'Antarctica/Syowa';
        $timezones[] = 'Antarctica/Troll';
        $timezones[] = 'Antarctica/Vostok';
        $timezones[] = 'Arctic/Longyearbyen';
        $timezones[] = 'Asia/Aden';
        $timezones[] = 'Asia/Almaty';
        $timezones[] = 'Asia/Amman';
        $timezones[] = 'Asia/Anadyr';
        $timezones[] = 'Asia/Aqtau';
        $timezones[] = 'Asia/Aqtobe';
        $timezones[] = 'Asia/Ashgabat';
        $timezones[] = 'Asia/Ashkhabad';
        $timezones[] = 'Asia/Atyrau';
        $timezones[] = 'Asia/Baghdad';
        $timezones[] = 'Asia/Bahrain';
        $timezones[] = 'Asia/Baku';
        $timezones[] = 'Asia/Bangkok';
        $timezones[] = 'Asia/Barnaul';
        $timezones[] = 'Asia/Beirut';
        $timezones[] = 'Asia/Bishkek';
        $timezones[] = 'Asia/Brunei';
        $timezones[] = 'Asia/Calcutta';
        $timezones[] = 'Asia/Chita';
        $timezones[] = 'Asia/Choibalsan';
        $timezones[] = 'Asia/Chongqing';
        $timezones[] = 'Asia/Chungking';
        $timezones[] = 'Asia/Colombo';
        $timezones[] = 'Asia/Dacca';
        $timezones[] = 'Asia/Damascus';
        $timezones[] = 'Asia/Dhaka';
        $timezones[] = 'Asia/Dili';
        $timezones[] = 'Asia/Dubai';
        $timezones[] = 'Asia/Dushanbe';
        $timezones[] = 'Asia/Famagusta';
        $timezones[] = 'Asia/Gaza';
        $timezones[] = 'Asia/Harbin';
        $timezones[] = 'Asia/Hebron';
        $timezones[] = 'Asia/Ho_Chi_Minh';
        $timezones[] = 'Asia/Hong_Kong';
        $timezones[] = 'Asia/Hovd';
        $timezones[] = 'Asia/Irkutsk';
        $timezones[] = 'Asia/Istanbul';
        $timezones[] = 'Asia/Jakarta';
        $timezones[] = 'Asia/Jayapura';
        $timezones[] = 'Asia/Jerusalem';
        $timezones[] = 'Asia/Kabul';
        $timezones[] = 'Asia/Kamchatka';
        $timezones[] = 'Asia/Karachi';
        $timezones[] = 'Asia/Kashgar';
        $timezones[] = 'Asia/Kathmandu';
        $timezones[] = 'Asia/Katmandu';
        $timezones[] = 'Asia/Khandyga';
        $timezones[] = 'Asia/Kolkata';
        $timezones[] = 'Asia/Krasnoyarsk';
        $timezones[] = 'Asia/Kuala_Lumpur';
        $timezones[] = 'Asia/Kuching';
        $timezones[] = 'Asia/Kuwait';
        $timezones[] = 'Asia/Macao';
        $timezones[] = 'Asia/Macau';
        $timezones[] = 'Asia/Magadan';
        $timezones[] = 'Asia/Makassar';
        $timezones[] = 'Asia/Manila';
        $timezones[] = 'Asia/Muscat';
        $timezones[] = 'Asia/Nicosia';
        $timezones[] = 'Asia/Novokuznetsk';
        $timezones[] = 'Asia/Novosibirsk';
        $timezones[] = 'Asia/Omsk';
        $timezones[] = 'Asia/Oral';
        $timezones[] = 'Asia/Phnom_Penh';
        $timezones[] = 'Asia/Pontianak';
        $timezones[] = 'Asia/Pyongyang';
        $timezones[] = 'Asia/Qatar';
        $timezones[] = 'Asia/Qostanay';
        $timezones[] = 'Asia/Qyzylorda';
        $timezones[] = 'Asia/Rangoon';
        $timezones[] = 'Asia/Riyadh';
        $timezones[] = 'Asia/Saigon';
        $timezones[] = 'Asia/Sakhalin';
        $timezones[] = 'Asia/Samarkand';
        $timezones[] = 'Asia/Seoul';
        $timezones[] = 'Asia/Shanghai';
        $timezones[] = 'Asia/Singapore';
        $timezones[] = 'Asia/Srednekolymsk';
        $timezones[] = 'Asia/Taipei';
        $timezones[] = 'Asia/Tashkent';
        $timezones[] = 'Asia/Tbilisi';
        $timezones[] = 'Asia/Tehran';
        $timezones[] = 'Asia/Tel_Aviv';
        $timezones[] = 'Asia/Thimbu';
        $timezones[] = 'Asia/Thimphu';
        $timezones[] = 'Asia/Tokyo';
        $timezones[] = 'Asia/Tomsk';
        $timezones[] = 'Asia/Ujung_Pandang';
        $timezones[] = 'Asia/Ulaanbaatar';
        $timezones[] = 'Asia/Ulan_Bator';
        $timezones[] = 'Asia/Urumqi';
        $timezones[] = 'Asia/Ust-Nera';
        $timezones[] = 'Asia/Vientiane';
        $timezones[] = 'Asia/Vladivostok';
        $timezones[] = 'Asia/Yakutsk';
        $timezones[] = 'Asia/Yangon';
        $timezones[] = 'Asia/Yekaterinburg';
        $timezones[] = 'Asia/Yerevan';
        $timezones[] = 'Atlantic/Azores';
        $timezones[] = 'Atlantic/Bermuda';
        $timezones[] = 'Atlantic/Canary';
        $timezones[] = 'Atlantic/Cape_Verde';
        $timezones[] = 'Atlantic/Faeroe';
        $timezones[] = 'Atlantic/Faroe';
        $timezones[] = 'Atlantic/Jan_Mayen';
        $timezones[] = 'Atlantic/Madeira';
        $timezones[] = 'Atlantic/Reykjavik';
        $timezones[] = 'Atlantic/South_Georgia';
        $timezones[] = 'Atlantic/St_Helena';
        $timezones[] = 'Atlantic/Stanley';
        $timezones[] = 'Australia/ACT';
        $timezones[] = 'Australia/Adelaide';
        $timezones[] = 'Australia/Brisbane';
        $timezones[] = 'Australia/Broken_Hill';
        $timezones[] = 'Australia/Canberra';
        $timezones[] = 'Australia/Currie';
        $timezones[] = 'Australia/Darwin';
        $timezones[] = 'Australia/Eucla';
        $timezones[] = 'Australia/Hobart';
        $timezones[] = 'Australia/LHI';
        $timezones[] = 'Australia/Lindeman';
        $timezones[] = 'Australia/Lord_Howe';
        $timezones[] = 'Australia/Melbourne';
        $timezones[] = 'Australia/NSW';
        $timezones[] = 'Australia/North';
        $timezones[] = 'Australia/Perth';
        $timezones[] = 'Australia/Queensland';
        $timezones[] = 'Australia/South';
        $timezones[] = 'Australia/Sydney';
        $timezones[] = 'Australia/Tasmania';
        $timezones[] = 'Australia/Victoria';
        $timezones[] = 'Australia/West';
        $timezones[] = 'Australia/Yancowinna';
        $timezones[] = 'Brazil/Acre';
        $timezones[] = 'Brazil/DeNoronha';
        $timezones[] = 'Brazil/East';
        $timezones[] = 'Brazil/West';
        $timezones[] = 'CET';
        $timezones[] = 'CST6CDT';
        $timezones[] = 'Canada/Atlantic';
        $timezones[] = 'Canada/Central';
        $timezones[] = 'Canada/Eastern';
        $timezones[] = 'Canada/Mountain';
        $timezones[] = 'Canada/Newfoundland';
        $timezones[] = 'Canada/Pacific';
        $timezones[] = 'Canada/Saskatchewan';
        $timezones[] = 'Canada/Yukon';
        $timezones[] = 'Chile/Continental';
        $timezones[] = 'Chile/EasterIsland';
        $timezones[] = 'Cuba';
        $timezones[] = 'EET';
        $timezones[] = 'EST';
        $timezones[] = 'EST5EDT';
        $timezones[] = 'Egypt';
        $timezones[] = 'Eire';
        $timezones[] = 'Etc/GMT';
        $timezones[] = 'Etc/GMT+0';
        $timezones[] = 'Etc/GMT+1';
        $timezones[] = 'Etc/GMT+10';
        $timezones[] = 'Etc/GMT+11';
        $timezones[] = 'Etc/GMT+12';
        $timezones[] = 'Etc/GMT+2';
        $timezones[] = 'Etc/GMT+3';
        $timezones[] = 'Etc/GMT+4';
        $timezones[] = 'Etc/GMT+5';
        $timezones[] = 'Etc/GMT+6';
        $timezones[] = 'Etc/GMT+7';
        $timezones[] = 'Etc/GMT+8';
        $timezones[] = 'Etc/GMT+9';
        $timezones[] = 'Etc/GMT-0';
        $timezones[] = 'Etc/GMT-1';
        $timezones[] = 'Etc/GMT-10';
        $timezones[] = 'Etc/GMT-11';
        $timezones[] = 'Etc/GMT-12';
        $timezones[] = 'Etc/GMT-13';
        $timezones[] = 'Etc/GMT-14';
        $timezones[] = 'Etc/GMT-2';
        $timezones[] = 'Etc/GMT-3';
        $timezones[] = 'Etc/GMT-4';
        $timezones[] = 'Etc/GMT-5';
        $timezones[] = 'Etc/GMT-6';
        $timezones[] = 'Etc/GMT-7';
        $timezones[] = 'Etc/GMT-8';
        $timezones[] = 'Etc/GMT-9';
        $timezones[] = 'Etc/GMT0';
        $timezones[] = 'Etc/Greenwich';
        $timezones[] = 'Etc/UCT';
        $timezones[] = 'Etc/UTC';
        $timezones[] = 'Etc/Universal';
        $timezones[] = 'Etc/Zulu';
        $timezones[] = 'Europe/Amsterdam';
        $timezones[] = 'Europe/Andorra';
        $timezones[] = 'Europe/Astrakhan';
        $timezones[] = 'Europe/Athens';
        $timezones[] = 'Europe/Belfast';
        $timezones[] = 'Europe/Belgrade';
        $timezones[] = 'Europe/Berlin';
        $timezones[] = 'Europe/Bratislava';
        $timezones[] = 'Europe/Brussels';
        $timezones[] = 'Europe/Bucharest';
        $timezones[] = 'Europe/Budapest';
        $timezones[] = 'Europe/Busingen';
        $timezones[] = 'Europe/Chisinau';
        $timezones[] = 'Europe/Copenhagen';
        $timezones[] = 'Europe/Dublin';
        $timezones[] = 'Europe/Gibraltar';
        $timezones[] = 'Europe/Guernsey';
        $timezones[] = 'Europe/Helsinki';
        $timezones[] = 'Europe/Isle_of_Man';
        $timezones[] = 'Europe/Istanbul';
        $timezones[] = 'Europe/Jersey';
        $timezones[] = 'Europe/Kaliningrad';
        $timezones[] = 'Europe/Kiev';
        $timezones[] = 'Europe/Kirov';
        $timezones[] = 'Europe/Lisbon';
        $timezones[] = 'Europe/Ljubljana';
        $timezones[] = 'Europe/London';
        $timezones[] = 'Europe/Luxembourg';
        $timezones[] = 'Europe/Madrid';
        $timezones[] = 'Europe/Malta';
        $timezones[] = 'Europe/Mariehamn';
        $timezones[] = 'Europe/Minsk';
        $timezones[] = 'Europe/Monaco';
        $timezones[] = 'Europe/Moscow';
        $timezones[] = 'Europe/Nicosia';
        $timezones[] = 'Europe/Oslo';
        $timezones[] = 'Europe/Paris';
        $timezones[] = 'Europe/Podgorica';
        $timezones[] = 'Europe/Prague';
        $timezones[] = 'Europe/Riga';
        $timezones[] = 'Europe/Rome';
        $timezones[] = 'Europe/Samara';
        $timezones[] = 'Europe/San_Marino';
        $timezones[] = 'Europe/Sarajevo';
        $timezones[] = 'Europe/Saratov';
        $timezones[] = 'Europe/Simferopol';
        $timezones[] = 'Europe/Skopje';
        $timezones[] = 'Europe/Sofia';
        $timezones[] = 'Europe/Stockholm';
        $timezones[] = 'Europe/Tallinn';
        $timezones[] = 'Europe/Tirane';
        $timezones[] = 'Europe/Tiraspol';
        $timezones[] = 'Europe/Ulyanovsk';
        $timezones[] = 'Europe/Uzhgorod';
        $timezones[] = 'Europe/Vaduz';
        $timezones[] = 'Europe/Vatican';
        $timezones[] = 'Europe/Vienna';
        $timezones[] = 'Europe/Vilnius';
        $timezones[] = 'Europe/Volgograd';
        $timezones[] = 'Europe/Warsaw';
        $timezones[] = 'Europe/Zagreb';
        $timezones[] = 'Europe/Zaporozhye';
        $timezones[] = 'Europe/Zurich';
        $timezones[] = 'Factory';
        $timezones[] = 'GB';
        $timezones[] = 'GB-Eire';
        $timezones[] = 'GMT';
        $timezones[] = 'GMT+0';
        $timezones[] = 'GMT-0';
        $timezones[] = 'GMT0';
        $timezones[] = 'Greenwich';
        $timezones[] = 'HST';
        $timezones[] = 'Hongkong';
        $timezones[] = 'Iceland';
        $timezones[] = 'Indian/Antananarivo';
        $timezones[] = 'Indian/Chagos';
        $timezones[] = 'Indian/Christmas';
        $timezones[] = 'Indian/Cocos';
        $timezones[] = 'Indian/Comoro';
        $timezones[] = 'Indian/Kerguelen';
        $timezones[] = 'Indian/Mahe';
        $timezones[] = 'Indian/Maldives';
        $timezones[] = 'Indian/Mauritius';
        $timezones[] = 'Indian/Mayotte';
        $timezones[] = 'Indian/Reunion';
        $timezones[] = 'Iran';
        $timezones[] = 'Israel';
        $timezones[] = 'Jamaica';
        $timezones[] = 'Japan';
        $timezones[] = 'Kwajalein';
        $timezones[] = 'Libya';
        $timezones[] = 'MET';
        $timezones[] = 'MST';
        $timezones[] = 'MST7MDT';
        $timezones[] = 'Mexico/BajaNorte';
        $timezones[] = 'Mexico/BajaSur';
        $timezones[] = 'Mexico/General';
        $timezones[] = 'NZ';
        $timezones[] = 'NZ-CHAT';
        $timezones[] = 'Navajo';
        $timezones[] = 'PRC';
        $timezones[] = 'PST8PDT';
        $timezones[] = 'Pacific/Apia';
        $timezones[] = 'Pacific/Auckland';
        $timezones[] = 'Pacific/Bougainville';
        $timezones[] = 'Pacific/Chatham';
        $timezones[] = 'Pacific/Chuuk';
        $timezones[] = 'Pacific/Easter';
        $timezones[] = 'Pacific/Efate';
        $timezones[] = 'Pacific/Enderbury';
        $timezones[] = 'Pacific/Fakaofo';
        $timezones[] = 'Pacific/Fiji';
        $timezones[] = 'Pacific/Funafuti';
        $timezones[] = 'Pacific/Galapagos';
        $timezones[] = 'Pacific/Gambier';
        $timezones[] = 'Pacific/Guadalcanal';
        $timezones[] = 'Pacific/Guam';
        $timezones[] = 'Pacific/Honolulu';
        $timezones[] = 'Pacific/Johnston';
        $timezones[] = 'Pacific/Kiritimati';
        $timezones[] = 'Pacific/Kosrae';
        $timezones[] = 'Pacific/Kwajalein';
        $timezones[] = 'Pacific/Majuro';
        $timezones[] = 'Pacific/Marquesas';
        $timezones[] = 'Pacific/Midway';
        $timezones[] = 'Pacific/Nauru';
        $timezones[] = 'Pacific/Niue';
        $timezones[] = 'Pacific/Norfolk';
        $timezones[] = 'Pacific/Noumea';
        $timezones[] = 'Pacific/Pago_Pago';
        $timezones[] = 'Pacific/Palau';
        $timezones[] = 'Pacific/Pitcairn';
        $timezones[] = 'Pacific/Pohnpei';
        $timezones[] = 'Pacific/Ponape';
        $timezones[] = 'Pacific/Port_Moresby';
        $timezones[] = 'Pacific/Rarotonga';
        $timezones[] = 'Pacific/Saipan';
        $timezones[] = 'Pacific/Samoa';
        $timezones[] = 'Pacific/Tahiti';
        $timezones[] = 'Pacific/Tarawa';
        $timezones[] = 'Pacific/Tongatapu';
        $timezones[] = 'Pacific/Truk';
        $timezones[] = 'Pacific/Wake';
        $timezones[] = 'Pacific/Wallis';
        $timezones[] = 'Pacific/Yap';
        $timezones[] = 'Poland';
        $timezones[] = 'Portugal';
        $timezones[] = 'ROC';
        $timezones[] = 'ROK';
        $timezones[] = 'Singapore';
        $timezones[] = 'SystemV/AST4';
        $timezones[] = 'SystemV/AST4ADT';
        $timezones[] = 'SystemV/CST6';
        $timezones[] = 'SystemV/CST6CDT';
        $timezones[] = 'SystemV/EST5';
        $timezones[] = 'SystemV/EST5EDT';
        $timezones[] = 'SystemV/HST10';
        $timezones[] = 'SystemV/MST7';
        $timezones[] = 'SystemV/MST7MDT';
        $timezones[] = 'SystemV/PST8';
        $timezones[] = 'SystemV/PST8PDT';
        $timezones[] = 'SystemV/YST9';
        $timezones[] = 'SystemV/YST9YDT';
        $timezones[] = 'Turkey';
        $timezones[] = 'UCT';
        $timezones[] = 'US/Alaska';
        $timezones[] = 'US/Aleutian';
        $timezones[] = 'US/Arizona';
        $timezones[] = 'US/Central';
        $timezones[] = 'US/East-Indiana';
        $timezones[] = 'US/Eastern';
        $timezones[] = 'US/Hawaii';
        $timezones[] = 'US/Indiana-Starke';
        $timezones[] = 'US/Michigan';
        $timezones[] = 'US/Mountain';
        $timezones[] = 'US/Pacific';
        $timezones[] = 'US/Pacific-New';
        $timezones[] = 'US/Samoa';
        $timezones[] = 'UTC';
        $timezones[] = 'Universal';
        $timezones[] = 'W-SU';
        $timezones[] = 'WET';
        $timezones[] = 'Zulu';

        return $timezones;
    }

    // Sets up the widget.
    public function widgetsInit($plugins) {
        register_widget('Clone_Guard_Widget');
    }   
}

// Creates instances of the API class and the main plugin class.
$cloneGuardSecurityAPI = new Clone_Guard_API();
$cloneGuardSecurityScanning = new Clone_Guard_Security_Scanning();
