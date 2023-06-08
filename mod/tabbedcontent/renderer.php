<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * RGU Tabbed Content module renderer.
 *
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

class mod_tabbedcontent_renderer extends plugin_renderer_base {

    function display_tabbed_content($instance, $contents, $cm, $type) {
        global $DB, $PAGE, $CFG;

        $context = context_module::instance($cm->id);

        $html = '';
        $tabs = '';
        $tabcontents = '';

        $isediting = mod_tabbedcontent_isediting();

        if ($instance->showname) {
            $html .= html_writer::tag('span', format_string($instance->name), array('class' => 'instancename clearfix'));
        }

        if ($cm->showdescription) {
            $instance->intro = file_rewrite_pluginfile_urls($instance->intro, 'pluginfile.php', $context->id, 'mod_tabbedcontent', 'intro', null);
            $html .= html_writer::tag('span', $instance->intro, array('class' => 'instanceintro clearfix'));
        }

        $args = array(
            array(
                'wwwroot' => $CFG->wwwroot,
                'tabswitcher' => '/mod/tabbedcontent/ajax/tabswitcher.php'
            )
        );

        if ($isediting) {
            $addurl  = new moodle_url('/mod/tabbedcontent/edit.php', array('cmid' => $cm->id));
            $addlink = html_writer::link($addurl, get_string('addnewtab', 'tabbedcontent'), array('class'=>'btn btn-primary'));
            $html .= html_writer::tag('div', $addlink, array('class' => 'mod-tabbedcontent-addnew '));
        }
        
        $html .= '<a name="tabbedcontent_'.$instance->id.'"></a>';  
        
      	$pack = html_writer::start_tag('div',array('class'=>'packunpacktabs'));  
        $pack .= html_writer::tag('div',html_writer::tag('a',html_writer::tag('i','',array('class'=>'fa fa-arrows-alt fa-lg','style'=>'color:#606;')),array('href'=>'#','onClick'=>'toggleTabs('.$instance->id.');return false;','title'=>get_string('unpack', 'tabbedcontent'))),array('class'=>'unpacktabs'));
        $pack .= html_writer::tag('div',html_writer::tag('a',html_writer::tag('i','',array('class'=>'fa fa-suitcase fa-lg','style'=>'color:#606;')),array('href'=>'#','onClick'=>'toggleTabs('.$instance->id.');return false;','title'=>get_string('pack', 'tabbedcontent'))).' '.html_writer::tag('a',html_writer::tag('i','',array('class'=>'fa fa-print fa-lg','style'=>'color:#606;')),array('href'=>'#','onClick'=>'printTabs('.$instance->id.');return false;','title'=>get_string('print', 'tabbedcontent'))),array('class'=>'packtabs'));
        $pack .= html_writer::tag('div',html_writer::tag('a',html_writer::tag('i','',array('class'=>'fa fa-close fa-lg','style'=>'color:#606;')).get_string('returntostudyarea', 'tabbedcontent'),array('href'=>'#','onClick'=>'closePrintTabs('.$instance->id.');return false;','title'=>get_string('returntostudyarea', 'tabbedcontent'))),array('class'=>'closeprinttabs'));
        $pack .= html_writer::end_tag('div'); 
        
        $tabbedcontent = html_writer::div(
                $this->render_outertabs($instance, $contents, $cm, $type),
                '',
                array(
                    'id' => 'rgumymoodle_outertabs_' . $instance->id, 'class' => ''
                )
            );

        $accordionclass = ($instance->type == 'vertical') ? 'accordion' : '';

        return html_writer::div($pack.html_writer::div($html . $tabbedcontent, $accordionclass, array()),null,array('id' => 'rgu_tabbed_content_'.$instance->id));
    }

    function render_outertabs($instance, $contents, $cm, $type, $tabid=NULL) {

        $tabs = '';
        $tabcontents = '';
        $isediting = mod_tabbedcontent_isediting();
       
        if ($contents) {
            $tabs = $this->render_tabs($contents, $tabid);
            $tabcontents = $this->render_tabcontents($contents, $cm->id, $isediting,$instance->type);
        }

        return $tabs . $tabcontents;
    }

    function render_tabs($tabs, $tabid) {

        $tabhtml = '';
        
        $isFirstTab = true;
        
        foreach ($tabs as $tab) {
        
         $class = 'nav-link';
            if($isFirstTab) {
                $class .= ' active';
                $isFirstTab = false;
            }
        
            $tabhtml .= html_writer::tag(
                    'li',
                    html_writer::link('#', $tab->tabtitle, array(
                        'class' => 'nav-link',
                        'role' => 'tab',
                        'data-toggle' => 'tab',
                        'data-target' => '#rgu_tab_content_'.$tab->id,
                        'id' => 'rgu_tab_title_'.$tab->id
                    )),
                    array(
                        'id' => "rgu_tabbed_content_tab_{$tab->id}",
                        'class' => "nav-item"
                    )
                );
        }

        return html_writer::tag('ul', $tabhtml, array('class' => 'nav nav-tabs', 'role'=>'tablist'));
    }

    function render_tabcontents($tabs, $cmid, $isediting,$type='') {
        global $CFG, $OUTPUT;
        
        $isFirstTab = true;

        $context = context_module::instance($cmid);

        $controlshtml = '';
        $tabcontentshtml = '';
        foreach ($tabs as $tab) {

            $tab->tabcontent = file_rewrite_pluginfile_urls($tab->tabcontent,
                    'pluginfile.php', $context->id, 'mod_tabbedcontent', 'content', $tab->id);

            if ($isediting) {
                $controlshtml = html_writer::start_tag('div',
                        array('class' => 'mod-tabbedcontent-controls')); // Open div .mod-tabbedcontent-controls.

                // 'Move left' icon.
                if (reset($tabs) != $tab) {
                    $leftlink = $OUTPUT->action_icon(
                            new moodle_url('/mod/tabbedcontent/movetab.php?',array('tab'=>$tab->id,'instance'=>$tab->instance,'direction'=>'left')),
                            
                            new pix_icon(
                                't/left',
                                get_string('movetableft', 'tabbedcontent'),
                                NULL,
                                array(
                                    'id' => TABBEDCONTENT_DECREASE . "_{$tab->instance}_{$tab->id}",
                                    'class' => "iconsmall tabcontrol_{$tab->instance}"
                                )
                            )
                        );
                    $controlshtml .= html_writer::tag(
                            'span',
                            $leftlink,
                            array(
                                'class' => 'mod-tabbedcontent-tabswitcher leftswitch'
                            )
                        );
                }

                // 'Move up' icon.
                if (reset($tabs) != $tab) {
                    $uplink = $OUTPUT->action_icon(
                            new moodle_url('/mod/tabbedcontent/movetab.php?',array('tab'=>$tab->id,'instance'=>$tab->instance,'direction'=>'up')),
                            new pix_icon(
                                't/up',
                                get_string('movetabup', 'tabbedcontent'),
                                NULL,
                                array(
                                    'id' => TABBEDCONTENT_DECREASE . "_{$tab->instance}_{$tab->id}",
                                    'class' => "iconsmall tabcontrol_{$tab->instance}"
                                )
                            )
                        );
                    $controlshtml .= html_writer::tag(
                            'span',
                            $uplink,
                            array(
                                'class' => 'mod-tabbedcontent-tabswitcher upswitch'
                            )
                        );
                }

                // 'Move right' icon.
                if (end($tabs) != $tab) {
                    $rightlink = $OUTPUT->action_icon(
                            new moodle_url('/mod/tabbedcontent/movetab.php?',array('tab'=>$tab->id,'instance'=>$tab->instance,'direction'=>'right')),
                 
                            new pix_icon(
                                't/right',
                                get_string('movetabright', 'tabbedcontent'),
                                NULL,
                                array(
                                    'id' => TABBEDCONTENT_INCREASE . "_{$tab->instance}_{$tab->id}",
                                    'class' => "iconsmall tabcontrol_{$tab->instance}"
                                )
                            )
                        );
                    $controlshtml .= html_writer::tag(
                            'span',
                            $rightlink,
                            array(
                                'class' => 'mod-tabbedcontent-tabswitcher rightswitch'
                            )
                        );
                }

                // 'Move down' icon.
                if (end($tabs) != $tab) {
                    $downlink = $OUTPUT->action_icon(
                            new moodle_url('/mod/tabbedcontent/movetab.php?',array('tab'=>$tab->id,'instance'=>$tab->instance,'direction'=>'down')),
                            

                            new pix_icon(
                                't/down',
                                get_string('movetabdown', 'tabbedcontent'),
                                NULL,
                                array(
                                    'id' => TABBEDCONTENT_INCREASE . "_{$tab->instance}_{$tab->id}",
                                    'class' => "iconsmall tabcontrol_{$tab->instance}"
                                )
                            )
                        );
                    $controlshtml .= html_writer::tag(
                            'span',
                            $downlink,
                            array(
                                'class' => 'mod-tabbedcontent-tabswitcher downswitch'
                            )
                        );
                }

                // 'Edit' icon.
                $editlink = $OUTPUT->action_icon(
                        new moodle_url('/mod/tabbedcontent/edit.php',
                                array('cmid' => $cmid, 'tab' => $tab->id)),
                        new pix_icon(
                            't/edit',
                            get_string('edit'),
                            NULL,
                            array(
                                'class' => 'iconsmall'
                            )
                        )
                    );
                $controlshtml .= html_writer::tag(
                        'span',
                        $editlink,
                        array(
                            'class' => 'mod-tabbedcontent-editlink'
                        )
                    );

                // 'Delete' icon.
                $deletelink = $OUTPUT->action_icon(
                        new moodle_url('/mod/tabbedcontent/delete.php',
                                array('cmid' => $cmid, 'id' => $tab->id)),
                        new pix_icon(
                            't/delete',
                            get_string('delete'),
                            null,
                            array(
                                'class' => 'iconsmall'
                            )
                        )
                    );
                $controlshtml .= html_writer::tag(
                        'span',
                        $deletelink,
                        array('class' => 'mod-tabbedcontent-deletelink')
                    );

                // Close controlshtml div
                $controlshtml .= html_writer::end_tag('div');
            }

            $options = new stdClass();
            $options->trusted = true;
            
            $hiddenheading = ($type === 'horizontal') ? html_writer::tag('div',html_writer::tag('h3',$tab->tabtitle),array('class'=>'rgutabheading')) : html_writer::tag('div','',array('class'=>'rgutabheading'));
            
            $class = 'tab-pane fade';
            if($isFirstTab) {
                $class .= ' active show';
                $isFirstTab = false;
            }
            
            $wrappedcontent = html_writer::tag('div',format_text($hiddenheading.$controlshtml.$tab->tabcontent, $tab->tabcontentformat, $options),array('class'=>$class, 'role'=>'tabpanel', 'aria-labelled-by'=>'rgu_tab_title_'.$tab->id, 'id'=>'rgu_tab_content_'.$tab->id));
            
            $tabcontentshtml .= $wrappedcontent;
        }

        return html_writer::div($tabcontentshtml, 'tab-content');
    }
}
