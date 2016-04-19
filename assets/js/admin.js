/**
 * Copyright (c) 2013-2016, Erin Morelli.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *
 * DLM Changelog admin javascript
 */

/*global
    jQuery,
    tinymce,
    ajaxurl
*/
/*jslint
    browser: true
    unparam: true
    nomen: true
*/

jQuery(document).ready(function ($) {
    'use strict';

    // Check for a hash in the URL
    if (location.hash) {
        // Add/remove active classes
        if (location.hash.slice(1) !== '') {
            $('#dlmcl-select-form').submit();
        }
    } else {
        // Get post slug
        var selected = $('#dlmcl-select-download'),
            selected_id = selected.val(),
            selected_hash = selected.find('option[value="' + selected_id + '"]').data('slug');

        // Set URL hash to slug
        if (selected_hash) {
            location.hash = selected_hash;
        } else {
            location.hash = '';
        }
    }

    // Handle select change events
    $('#dlmcl-select-download').on('change', function () {
        $('#dlmcl-select-form').submit();
    });

    // Set placeholder text content
    function dlmcl_set_placeholder(editor) {
        if (editor.getContent() === '') {
            $(editor.bodyElement).addClass('dlmcl-editable-placeholder');
        } else {
            $(editor.bodyElement).removeClass('dlmcl-editable-placeholder');
        }
    }

    // Remove success/error classes
    function dlmcl_remove_classes(editor) {
        // Get elements
        var element = $(editor.bodyElement),
            editor_element = $('#' + editor.theme.panel._id),
            save_button = editor_element.find('.mce-i-save').offsetParent().parent();

        // Remove save class
        $(save_button).removeClass('dlmcl-save-success');
        element.removeClass('dlmcl-save-success');

        // Remove error class
        $(save_button).removeClass('dlmcl-save-error');
        element.removeClass('dlmcl-save-error');
    }

    // Show save error
    function dlmcl_save_error(editor, element, data) {
        // Get elements
        var editor_element = $('#' + editor.theme.panel._id),
            save_button = editor_element.find('.mce-i-save').offsetParent().parent();

        // Add save class
        $(save_button).addClass('dlmcl-save-error');
        element.addClass('dlmcl-save-error');

        // Report error
        console.error('There was a problem saving the changelog note:', data);
    }

    // Show save success
    function dlmcl_save_success(editor, element, data) {
        // Get elements
        var editor_element = $('#' + editor.theme.panel._id),
            save_button = editor_element.find('.mce-i-save').offsetParent().parent();

        // Add save class
        $(save_button).addClass('dlmcl-save-success');
        element.addClass('dlmcl-save-success');
    }

    // Initialize TinyMCE inline editor
    tinymce.init({
        selector: 'div.dlmcl-editable',
        inline: true,
        menubar: false,
        resize: true,
        statusbar: true,
        body_class: 'dlmcl-editable-body',
        plugins: [
            'advlist autolink autosave autoresize lists link tabfocus',
            'visualblocks code paste textcolor colorpicker save'
        ],
        toolbar: [
            'undo redo | save',
            'styleselect forecolor | bold italic underline | bullist numlist outdent indent | link code'
        ],
        init_instance_callback: dlmcl_set_placeholder,
        setup: function (editor) {
            // Check if we need to show the placeholder
            editor.on('change', function (e) {
                dlmcl_set_placeholder(editor);
                dlmcl_remove_classes(editor);
            });

            // Hide placeholder on focus
            editor.on('focus', function (e) {
                $(editor.bodyElement).removeClass('dlmcl-editable-placeholder');
            });

            // Check if we need to show the placeholder on blur
            editor.on('blur', function (e) {
                dlmcl_set_placeholder(editor);
                dlmcl_remove_classes(editor);
            });
        },
        save_onsavecallback: function () {
            // Set up post save AJAX data
            var ed = tinymce.activeEditor,
                element = $(this.bodyElement),
                data = {
                    'action': 'dlmcl_save_post',
                    'post_id': $(this.bodyElement).data('id'),
                    'post_content': this.getContent()
                };

            // Make AJAX POST call to WP
            $.post(ajaxurl, data, function (response) {
                // Handle empty save responses
                if (response === '') {
                    return dlmcl_save_error(ed, element, data);
                }

                // Display success styles
                return dlmcl_save_success(ed, element, data);
            }).fail(function () {
                return dlmcl_save_error(ed, element, data);
            });
        }
    });
});
