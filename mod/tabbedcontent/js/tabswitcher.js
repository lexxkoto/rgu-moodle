/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    block
 * @subpackage rgu_course_overview
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

M.mod_tabbedcontent = {};

M.mod_tabbedcontent.tabswitcher = function(Y, options) {

YUI.add('easyResponsiveTabs', function(Y) {
    Y.External = {
        run: function() {
            alert('External Module was loaded.');
        }
    }
}, '1.0.0', { requires: [ 'node' ] });

    Y.all('div.mod-tabbedcontent-controls span.mod-tabbedcontent-tabswitcher a.action-icon i').detach();
   Y.all('div.mod-tabbedcontent-controls span.mod-tabbedcontent-tabswitcher a.action-icon i').on('click',
            function(e, tabswitcher, wwwroot) {
	return true;
        e.preventDefault();
        var imgid = e._currentTarget.parentNode.parentNode.id;
	alert(imgid);
        var items = imgid.split('_');
        var tabdirection = items[0];
        var tabinstance = items[1];
        var tabid = items[2];

        var on;
        var params = [];
        params['tabdirection'] = tabdirection;
        params['tabinstance'] = tabinstance;
        params['tabid'] = tabid;
        Y.io(wwwroot + tabswitcher, {
            method: 'POST',
            data: build_querystring(params),
            on: {
                start: null,
                complete: function(tid, outcome, args) {
                    var result = Y.JSON.parse(outcome.responseText);
                    if (result) {
                        if (result.html) {
                            var html = result.html;
                            Y.one('#rgumymoodle_outertabs_'+tabinstance).setContent(html);
                        }
                        $('#rgumymoodle_outertabs_'+tabinstance).easyResponsiveTabs({
                            type: 'default',             // Types: default, vertical, accordion
                            width: 'auto',               // auto or any width like 600px
                            fit: true,                   // 100% fit in a container
                            tabidentify: 'hor_1',        // The tab groups identifier
                            activate: function (event) { // Callback function if tab is switched
                                var $tab = $(this);
                                var $info = $('#nested-tabInfo');
                                var $name = $('span', $info);
                                $name.text($tab.text());
                                $info.show();
                            }
                        });
                        $('#ChildVerticalTab_1').easyResponsiveTabs({
                            type: 'vertical',
                            width: 'auto',
                            fit: true,
                            tabidentify: 'ver_1',                  // The tab groups identifier
                            activetab_bg: '#fff',                  // background color for active tabs in this group
                            inactive_bg: '#F5F5F5',                // background color for inactive tabs in this group
                            active_border_color: '#c1c1c1',        // border color for active tabs heads in this group
                            active_content_border_color: '#5AB1D0' // border color for active tabs contect in this group so that it matches the tab head border
                        });
                        $('#rgu_tabbed_content_tab_'+tabid).click();
                        M.mod_tabbedcontent.tabswitcher(Y, options);
                    }
                },
                end: null,
            },
            context: this,
            arguments: {}
        });
    }, this, options.tabswitcher, options.wwwroot);
};




