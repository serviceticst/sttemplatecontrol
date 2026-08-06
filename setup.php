<?php

/**
 * SPDX-FileCopyrightText: 2026 Curtis Conard
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

use Glpi\Plugin\Hooks;

function plugin_init_unsticktemplates()
{
    global $PLUGIN_HOOKS;

    $fn_unstick_templates_id = static function (CommonITILObject $item) {
        $tpl_class = $item::getTemplateClass();
        $template_field = $tpl_class::getForeignKeyField();
        unset($item->input[$template_field]);
    };

    $PLUGIN_HOOKS[Hooks::POST_PREPAREADD]['unsticktemplates'] = [
        Ticket::class => $fn_unstick_templates_id,
        Change::class => $fn_unstick_templates_id,
        Problem::class => $fn_unstick_templates_id,
    ];
}

function plugin_version_unsticktemplates()
{
    return [
        'name'         => __('Unstick ITIL Templates', 'unsticktemplates'),
        'version'      => '1.0.0',
        'author'       => 'Curtis Conard',
        'license'      => 'GPLv3+',
        'homepage'     =>'https://github.com/cconard96/glpi-unsticktemplates-plugin',
        'requirements' => [
            'glpi'   => [
                'min' => '11.0.0',
                'max' => '12.0.0'
            ]
        ]
    ];
}
