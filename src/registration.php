<?php
/**
 * Created with Nanobots_DataPatchCreator
 * @author      Jakub Winkler <jwinkler@qsolutionsstudio.com>
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 * @author      Wojtek Wnuk <wojtek@qsolutionsstudio.com>
 *
 * @category    {{var namespace}}
 * @package     {{var namespace}}_{{var module}}
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Nanobots_DataPatchCreator',
    __DIR__
);
