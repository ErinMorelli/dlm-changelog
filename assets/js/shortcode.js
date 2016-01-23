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
 * DLM Changelog AJAX Shortcode Pagination
 */

/*global
    jQuery,
    dlmcl_load
*/
/*jslint
    browser: true
*/

jQuery(document).ready(function ($) {
    'use strict';

    var pageNum = parseInt(dlmcl_load.startPage, 10) + 1,   // The number of the next page to load (/page/x/).
        max = parseInt(dlmcl_load.maxPages, 10),            // The maximum number of pages the current query can return.
        nextLink = dlmcl_load.nextLink,                     // The link of the next page of posts.
        moreText = dlmcl_load.moreText,                     // Button text
        loadText = dlmcl_load.loadText;

    if (pageNum <= max) {
        // Insert the "More Posts" link.
        $('.dlm-changelog-list').append('<div class="dlm-changelog-placeholder-' + pageNum + '"></div>');
        $('.dlm-changelog-list').append('<p class="dlm-changelog-load-posts"><a href="#" class="dlmcl-button">' + moreText + '</a></p>');
    }

    /**
     * Load new posts when the link is clicked.
     */
    $('.dlm-changelog-load-posts a').click(function () {

        // Are there more posts to load?
        if (pageNum < max) {

            // Show that we're working.
            $(this).text(loadText + '...');

            $('.dlm-changelog-placeholder-' + pageNum).load(nextLink + ' .dlm-changelog-item', function () {
                // Update page number and nextLink.
                pageNum += 1;
                nextLink = '?more=' + pageNum;

                // Add a new placeholder, for when user clicks again.
                $('.dlm-changelog-load-posts').before('<div class="dlm-changelog-placeholder-' + pageNum + '"></div>');

                // Update the button message.
                if (pageNum < max) {
                    $('.dlm-changelog-load-posts a').text(moreText);
                } else {
                    $('.dlm-changelog-load-posts a').hide();
                }
            });
        } else {
            $('.dlm-changelog-load-posts a').append('.');
        }

        return false;
    });
});
