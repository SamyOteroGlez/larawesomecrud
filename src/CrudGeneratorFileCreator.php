<?php

namespace CrudGenerator;

use DB;
use Artisan;
use Illuminate\Console\Command;


class CrudGeneratorFileCreator
{
    private $options;
    private $cmdObject;
    private $templateName;
    private $path;
    private $deletePrevious;
    private $templateFolder;
    private $templateEngine = null;


    /**
     * New CrudGeneratorFileCreator instance.
     *
     * @param array $options
     * @param null $output
     * @param string $templateName
     * @param string $path
     * @param bool|false $deletePrevious
     */
    public function __construct(
      $options = [],
      $cmdObject = null,
      $templateName = '',
      $path = '',
      $deletePrevious = false,
      $templateFolder
    ) {
        $this->options = $options;
        $this->cmdObject = $cmdObject;
        $this->templateName = $templateName;
        $this->path = $path;
        $this->deletePrevious = $deletePrevious;
        $this->templateFolder = $templateFolder;
        $this->cmdObject->info('FileCreator Constructor ' . $templateFolder);
    }

    public function Generate($templateName)
    {
        $this->cmdObject->info('Template Name: ' . $templateName);
        $loader = new \Twig_Loader_Filesystem($this->templateFolder);

        $this->templateEngine = new \Twig_Environment($loader, array(
          'cache' => false,
          'debug' => true,
          'optimization' => false
        ));
        $lexer = new \Twig_Lexer($this->templateEngine, array(
          'tag_comment' => array('[#', '#]'),
          'tag_block' => array('[%', '%]'),
          'tag_variable' => array('[[', ']]'),
          'interpolation' => array('#[', ']'),
        ));
        $this->templateEngine->setLexer($lexer);

        $renderedContent = $this->templateEngine->render($templateName . '.twig', $this->options);

        file_put_contents($this->path, $renderedContent);

        //$this->output->info('Created Controller: ' . $this->path);
    }


    protected function renderVariables($template, $data)
    {
        $callback = function ($matches) use ($data) {

            if (array_key_exists($matches[1], $data)) {
                return $data[$matches[1]];
            }

            return $matches[0];
        };
        $template = preg_replace_callback('/\[\[\s*(.+?)\s*\]\](\r?\n)?/s', $callback, $template);

        return $template;
    }

    protected function renderForeachs($template, $data)
    {
        $callback = function ($matches) use ($data) {
            $rep = $matches[0];
            $rep = preg_replace('/\[\[\s*foreach:\s*(.+?)\s*\]\](\r?\n)?/s', '', $rep);
            $rep = preg_replace('/\[\[\s*endforeach\s*\]\](\r?\n)?/s', '', $rep);
            $ret = '';

            if (array_key_exists($matches[1], $data) && is_array($data[$matches[1]])) {
                $parent = $data[$matches[1]];

                foreach ($parent as $i) {
                    $d = [];

                    if (is_array($i)) {

                        foreach ($i as $key => $value) {
                            $d['i.' . $key] = $value;
                        }
                    } else {
                        $d['i'] = $i;
                    }
                    $rep2 = $this->renderIFs($rep, array_merge($d, $data));
                    $rep2 = $this->renderVariables($rep2, array_merge($d, $data));
                    $ret .= $rep2;
                }
                return $ret;
            } else {
                return $ret;
            }
        };
        $template = preg_replace_callback('/\[\[\s*foreach:\s*(.+?)\s*\]\](\r?\n)?((?!endforeach).)*\[\[\s*endforeach\s*\]\](\r?\n)?/s',
          $callback, $template);

        return $template;
    }

    protected function getValFromExpression($exp, $data)
    {
        if (str_contains($exp, "'")) {
            return trim($exp, "'");
        } else {

            if (array_key_exists($exp, $data)) {
                return $data[$exp];
            } else {
                return null;
            }
        }
    }

    protected function renderIFs($template, $data)
    {
        $callback = function ($matches) use ($data) {
            $rep = $matches[0];
            $rep = preg_replace('/\[\[\s*if:\s*(.+?)\s*([!=]=)\s*(.+?)\s*\]\](\r?\n)?/s', '', $rep);
            $rep = preg_replace('/\[\[\s*endif\s*\]\](\r?\n)?/s', '', $rep);
            $ret = '';
            $val1 = $this->getValFromExpression($matches[1], $data);
            $val2 = $this->getValFromExpression($matches[3], $data);

            if ($matches[2] == '==' && $val1 == $val2) {
                $ret .= $rep;
            }
            if ($matches[2] == '!=' && $val1 != $val2) {
                $ret .= $rep;
            }

            return $ret;
        };
        $template = preg_replace_callback('/\[\[\s*if:\s*(.+?)\s*([!=]=)\s*(.+?)\s*\]\](\r?\n)?((?!endif).)*\[\[\s*endif\s*\]\](\r?\n)?/s',
          $callback, $template);

        return $template;
    }

    protected function getCompleteTemplateName($template_name, $folder = null)
    {

        return $folder . '/' . $template_name . '.twig';
    }

    /**
     * Set deletePrevious.
     *
     * @param $deletePrevious
     */
    public function setDeletePreviousAttribute($deletePrevious)
    {
        $this->deletePrevious = $deletePrevious;
    }

    /**
     * Get deletePrevious.
     *
     * @return bool|false
     */
    public function getDeletePreviousAttribute()
    {
        return $this->deletePrevious;
    }

    /**
     * Set path.
     *
     * @param $path
     */
    public function setPathAttribute($path)
    {
        $this->path = $path;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPathAttribute()
    {
        return $this->path;
    }

    /**
     * Set templateName.
     *
     * @param $templateName
     */
    public function setTemplateNameAttribute($templateName)
    {
        $this->templateName = $templateName;
        //$this->output->info('Template Name Attribute: '.$templateName);
    }

    /**
     * Get templateName.
     *
     * @return string
     */
    public function getTemplateNameAttribute()
    {
        return $this->templateName;
    }

    /**
     * Set output.
     *
     * @param $output
     */
    public function setOutputAttribute($output)
    {
        $this->cmdObject = $output;
    }

    /**
     * Get output.
     *
     * @return null
     */
    public function getOutputAttribute()
    {
        return $this->cmdObject;
    }

    /**
     * Set options.
     *
     * @param $options
     */
    public function setOptionsAttribute($options)
    {
        $this->options = $options;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        return $this->options;
    }

    /**
     *
     * @return null
     */
    public function getTemplateFolder()
    {
        return $this->templateFolder;
    }

    /**
     * @param null $templateFolder
     */
    public function setTemplateFolder($templateFolder)
    {
        $this->templateFolder = $templateFolder;
    }

}
