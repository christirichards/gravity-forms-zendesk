<?php
/*
Plugin Name: Gravity Forms Zendesk Add-On
Plugin URI: https://github.com/christirichards/gravity-forms-zendesk/
Description: Integrates Gravity Forms with Zendesk allowing form submissions to be automatically sent as a new ticket to your Zendesk account.
Version: 0.1
Author: Christi Richards
Author URI: http://www.christirichards.com

------------------------------------------------------------------------
Copyright 2015 - Christi Richards

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

//------------------------------------------
if (class_exists("GFForms")) {
    GFForms::include_feed_addon_framework();

    class GFgfzendesk extends GFFeedAddOn {

        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.7.9999";
        protected $_slug = "gf_zendesk";
        protected $_path = "gravity-forms-zendesk/zendesk.php";
        protected $_full_path = __FILE__;
        protected $_title = "Gravity Forms Zendesk Add-On";
        protected $_short_title = "Zendesk";


        public function feed_settings_fields() {
            return array(
                array(
                    "title"  => "Zendesk Feed Settings",
                    "fields" => array(
                        array(
                            "label"   => "Feed Name", 
                            "type"    => "text",
                            "name"    => "feedName",
                            "tooltip" => "Enter a unique identifying name for this feed (ie. Contact Us Zendesk Tickets)",
                            "class"   => "small"
                        ),
                        array(
                            "name" => "mappedFields",
                            "label" => "Map Fields",
                            "type" => "field_map",
                            "field_map" => array(   array("name" => "email", "label" => "E-mail", "required" => 0),
                                                    array("name" => "firstname", "label" => "First Name", "required" => 0),
                                                    array("name" => "lastname", "label" => "Last Name", "required" => 0),
                                                    array("name" => "subject", "label" => "Ticket Subject", "required" => 0),
                                                    array("name" => "body", "label" => "Ticket Body", "required" => 0),
                                                    array("name" => "upload", "label" => "Ticket Upload(s)", "required" => 0)
                            )
                        ),
                        array(
                            "name" => "condition",
                            "label" => __("Condition", "gfzendesk"),
                            "type" => "feed_condition",
                            "checkbox_label" => __("Enable Condition", "gfzendesk"),
                            "instructions" => __("Process this Zendesk feed if", "gfzendesk")
                        ),
                    )
                )
            );
        }
           
        public function feed_list_columns() {
            return array(
                "feedName" => __("Feed Name", "gfzendesk"), 
                "account" => __("Zendesk Account", "gfzendesk") 
            );
        }

        public function plugin_page() {
            
            // Placeholder for Admin Menu Plugin page
        
        }

        public function get_column_value_account($feed) {

            return "<b>" . $this->get_plugin_setting("zendesk_domain") ."</b>";  

        }

        public function plugin_settings_fields() {
            return array(
                    array(
                        "title"  => "Zendesk Account Settings",
                        "description" => sprintf(
                        __( '<p>Zendesk delivers the leading cloud-based customer service software. Use Gravity Forms to collect user information and automatically add the information as a Zendesk ticket. If you don\'t have a Zendesk account, you can %1$s sign up for one here.%2$s</p>', 'gfzendesk' ),
                        '<a href="https://www.zendesk.com/register/#getstarted" target="_blank">', '</a>'
                    ),
                    "fields" => array(
                        array(
                            "name"              => "api_key",
                            "label"             => __( "Zendesk API Key", "gfzendesk" ),
                            "type"              => "text",
                            "default_value"     => "",
                            "class"             => "medium",
                            "feedback_callback" => array( $this, "is_valid_api_key" ) // TO DO: Start writing initial API credential validation
                        ),
                        array(
                            "name"              => "api_email",
                            "label"             => __( "Zendesk API E-mail", "gfzendesk" ),
                            "type"              => "text",
                            "default_value"     => "",
                            "class"             => "medium",
                        ),
                        array(
                            "name"              => "zendesk_domain",
                            "label"             => __( "Zendesk Domain", "gfzendesk" ),
                            "type"              => "text",
                            "default_value"     => "",
                            "class"             => "medium",
                        )
                    )
                )
            );
        }

        public function scripts() {
            $scripts = array(
                array("handle"  => "zendesk_scripts",
                      "src"     => $this->get_base_url() . "/js/scripts.js",
                      "version" => $this->_version,
                      "deps"    => array("jquery"),
                      "enqueue" => array(
                          array(
                              "admin_page" => array("form_settings"),
                              "tab"        => "gfzendesk"
                          )
                      )
                ),

            );

            return array_merge(parent::scripts(), $scripts);
        }

        public function styles() {
            $styles = array(
                array("handle"  => "zendesk_styles",
                      "src"     => $this->get_base_url() . "/css/style.css",
                      "version" => $this->_version,
                      "enqueue" => array(
                          array(
                              "admin_page" => array("form_settings"),
                              "tab"        => "gfzendesk"
                          )
                      )
                )
            );

            return array_merge(parent::styles(), $styles);
        }

        public function process_feed($feed, $entry, $form){
            $feedName = $feed["meta"]["feedName"];
            $mapped_email = $feed["meta"]["mappedFields_email"];
            $mapped_firstname = $feed["meta"]["mappedFields_firstname"];
            $mapped_lastname = $feed["meta"]["mappedFields_lastname"];
            $mapped_subject = $feed["meta"]["mappedFields_subject"];
            $mapped_body = $feed["meta"]["mappedFields_body"];
            $mapped_upload = $feed["meta"]["mappedFields_upload"];

            $email = $entry[$mapped_email];
            $firstname = $entry[$mapped_firstname];
            $lastname = $entry[$mapped_lastname];
            $subject = $entry[$mapped_subject];
            $body = $entry[$mapped_body];
            $upload = $entry[$mapped_upload];
        }
    }

    new GFgfzendesk();
}