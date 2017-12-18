<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) elements.at New Media Solutions GmbH (http://www.elements.at)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace ExportToolkit\ExportService\Getter;

use ExportToolkit\ExportService\IGetter;

class DefaultBrickGetterSequence implements IGetter
{
    public static function get($object, $config = null)
    {
        $sourceList = $config->source;

        foreach ($sourceList as $source) {
            $brickContainerGetter = 'get' . ucfirst($source->brickfield);

            if (method_exists($object, $brickContainerGetter)) {
                $brickContainer = $object->$brickContainerGetter();

                $brickGetter = 'get' . ucfirst($source->bricktype);
                $brick = $brickContainer->$brickGetter();
                if ($brick) {
                    $fieldGetter = 'get' . ucfirst($source->fieldname);
                    $value = $brick->$fieldGetter();
                    if ($value) {
                        return $value;
                    }
                }
            }
        }
    }
}
