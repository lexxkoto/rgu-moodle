/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 - 2013 Totara Learning Solutions LTD
 *
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

M.block_rgu_course_overview = {};

M.block_rgu_course_overview.init_outertabs1 = function(Y, options) {
    Y.all('div.rgu_course_overview_pagination_current a.rgu_course_overview_paginator').detach();
    Y.all('div.rgu_course_overview_pagination_current a.rgu_course_overview_paginator').on('click',
            function(e, updatepanel, wwwroot) {
        e.preventDefault();
        var page = e._currentTarget.text;
        var panel = e._currentTarget.parentNode.parentNode.id;
        var blockinstance = e._currentTarget.parentNode.id;
        blockinstance = blockinstance.replace('block_rgu_course_overview_', '');
        if (page) {
            var on;
            var params = [];
            params['page'] = page;
            params['panel'] = panel;
            params['blockinstance'] = blockinstance;
            Y.io(wwwroot + updatepanel, {
                method: 'POST',
                data: build_querystring(params),
                on: {
                    start: null,
                    complete: function(tid, outcome, args) {
                        var result = Y.JSON.parse(outcome.responseText);
                        if (result) {
                            if (result.html) {
                                var html = result.html;
                                Y.one('#'+panel).setContent(html);
                            }
                            M.block_rgu_course_overview.init_outertabs1(Y, options);
                        }
                    },
                    end: null,
                },
                context: this,
                arguments: {}
            });
        }
    }, this, options.updatepanel, options.wwwroot);
};

M.block_rgu_course_overview.init_outertabs2 = function(Y, options) {
    Y.all('div.rgu_course_overview_pagination_previous a.rgu_course_overview_paginator').detach();
    Y.all('div.rgu_course_overview_pagination_previous a.rgu_course_overview_paginator').on('click',
            function(e, updatepreviouspanel, wwwroot) {
        e.preventDefault();
        var page = e._currentTarget.text;
        var panelid = e._currentTarget.parentNode.parentNode.id;
        var year = panelid.replace('modules', '');
        var blockinstance = e._currentTarget.parentNode.id;
        blockinstance = blockinstance.replace('block_rgu_course_overview_', '');
        if (page) {
            var on;
            var params = [];
            params['page'] = page;
            params['year'] = year;
            params['blockinstance'] = blockinstance;
            Y.io(wwwroot + updatepreviouspanel, {
                method: 'POST',
                data: build_querystring(params),
                on: {
                    start: null,
                    complete: function(tid, outcome, args) {
                        var result = Y.JSON.parse(outcome.responseText);
                        if (result) {
                            if (result.html) {
                                var html = result.html;
                                Y.one('#modules'+year).setContent(html);
                            }
                            M.block_rgu_course_overview.init_outertabs2(Y, options);
                        }
                    },
                    end: null,
                },
                context: this,
                arguments: {}
            });
        }
    }, this, options.updatepreviouspanel, options.wwwroot);
};

M.block_rgu_course_overview.init_outertabs3 = function(Y, options) {
    Y.all('img.rgumymoodle_rgumymoodle_searchicon').detach();
    Y.one('img.rgumymoodle_rgumymoodle_searchicon').on('click',
            function(e, updatesearchresults, wwwroot) {
        e.preventDefault();

        var searchtext = Y.one('input#rgumymoodle_studyareasearchbox').get('value');
        var blockinstance = e._currentTarget.id;
        blockinstance = blockinstance.replace('rgumymoodle_rgumymoodle_searchicon_', '');
        if (searchtext) {
            var on;
            var params = [];
            params['page'] = 1;
            params['searchtext'] = searchtext;
            params['blockinstance'] = blockinstance;
            Y.io(wwwroot + updatesearchresults, {
                method: 'POST',
                data: build_querystring(params),
                on: {
                    start: null,
                    complete: function(tid, outcome, args) {
                        var result = Y.JSON.parse(outcome.responseText);
                        if (result) {
                            if (result.html) {
                                var html = result.html;
                                Y.one('#rgu_course_overview_search_results').setContent(html);
                            }
                            M.block_rgu_course_overview.init_outertabs3(Y, options);
                            M.block_rgu_course_overview.init_searchpaginator(Y, options);
                        }
                    },
                    end: null,
                },
                context: this,
                arguments: {}
            });
        }
    }, this, options.updatesearchresults, options.wwwroot);
};

M.block_rgu_course_overview.init_searchpaginator = function(Y, options) {
    Y.all('div.rgu_course_overview_pagination_search a.rgu_course_overview_paginator').detach();
    Y.all('div.rgu_course_overview_pagination_search a.rgu_course_overview_paginator').on('click',
            function(e, wwwroot) {
        e.preventDefault(); 
        var page = e._currentTarget.text;
        var searchtext = Y.one('input#rgumymoodle_studyareasearchbox').get('value');
        var blockinstance = e._currentTarget.parentNode.id;
        blockinstance = blockinstance.replace('block_rgu_course_overview_', '');
        if (searchtext) {
            var on;
            var params = [];
            params['page'] = page;
            params['searchtext'] = searchtext;
            params['blockinstance'] = blockinstance;
            Y.io(wwwroot + '/blocks/rgu_course_overview/ajax/updatesearchresults.php', {
                method: 'POST',
                data: build_querystring(params),
                on: {
                    start: null,
                    complete: function(tid, outcome, args) {
                        var result = Y.JSON.parse(outcome.responseText);
                        if (result) {
                            if (result.html) {
                                var html = result.html;
                                Y.one('#rgu_course_overview_search_results').setContent(html);
                            }
                        }
                        M.block_rgu_course_overview.init_searchpaginator(Y, options);
                    },
                    end: null,
                },
                context: this,
                arguments: {}
            });
        }
    }, this, options.wwwroot);
};
