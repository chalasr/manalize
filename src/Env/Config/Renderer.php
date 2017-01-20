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

use Manala\Manalize\Env\Config\Variable\Variable;

/**
 * Config' template renderer.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Renderer
{
    const TEMPLATE_CACHE_DIR = MANALIZE_DIR.'/var/cache';

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct()
    {
        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(['/', MANALIZE_DIR.'/src/Resources'], '/'),
            ['cache' => self::TEMPLATE_CACHE_DIR]
        );
        $twig->setLexer(new \Twig_Lexer($twig, ['tag_comment' => ['[#', '#]'], 'tag_variable' => ['{#', '#}']]));

        $this->twig = $twig;
    }

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

        $allVars = $config->getVars();
        $vars = array_map(function (Variable $var) {
            return $var->getReplaces();
        }, $allVars);

        $context = [];
        foreach ($config->getVars() as $var) {
            $context = array_merge($context, $var->getReplaces());
        }

        return $this->twig->render($template, $context);
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
