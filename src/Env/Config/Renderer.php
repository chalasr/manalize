<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config;

/**
 * Config' template renderer.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Renderer
{
    /**
     * Renders a config template.
     *
     * @param Config $config The whole Config for which to dump the template
     *
     * @return string
     *
     * @throws \InvalidArgumentException If the config template is not readable
     */
    public function render(Config $config): string
    {
        $template = $config->getTemplate();

        if (!is_readable($template)) {
            throw new \RuntimeException(sprintf(
                'The template file "%s" is either not readable or doesn\'t exist.',
                $template
            ));
        }

        $vars = $config->getVars();
        $rendered = self::renderIncludes(file_get_contents($template));

        foreach ($vars as $var) {
            $rendered = strtr($rendered, $var->getReplaces());
        }

        return $rendered;
    }

    private static function renderIncludes(string $template)
    {
        preg_match_all('/{% include (.*) %}/', $template, $matches);

        foreach ($matches[1] as $include) {
            $template = str_replace("{% include $include %}", file_get_contents(MANALIZE_DIR.'/src/Resources/'.$include), $template);
        }

        return $template;
    }
}
