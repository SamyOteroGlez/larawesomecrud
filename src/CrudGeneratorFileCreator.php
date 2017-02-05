<?php

namespace CrudGenerator;

use DB;
use Artisan;
use Illuminate\Console\Command;

class CrudGeneratorFileCreator 
{
    private $options;
    private $output;
    private $templateName;
    private $path;
    private $deletePrevious;

    /**
     * New CrudGeneratorFileCreator instance.
     *
     * @param array $options
     * @param null $output
     * @param string $templateName
     * @param string $path
     * @param bool|false $deletePrevious
     */
    public function __construct($options = [], $output = null, $templateName = '', $path = '', $deletePrevious = false)
    {
        $this->options = $options;
        $this->output = $output;
        $this->templateName = $templateName;
        $this->path = $path;
        $this->deletePrevious = $deletePrevious;
    }

    public function Generate()
    {
        $c = $this->renderWithData($this->customTemplateOfDefault($this->templateName), $this->options);
        file_put_contents($this->path, $c);
        $this->output->info('Created Controller: '.$this->path);
    }

    protected function renderWithData($template_path, $data)
    {
        $template = file_get_contents($template_path);
        $template = $this->renderForeachs($template, $data);
        $template = $this->renderIFs($template, $data);
        $template = $this->renderVariables($template, $data);

        return $template;
    }

    protected function renderVariables($template, $data)
    {
        $callback = function ($matches) use($data) {

            if(array_key_exists($matches[1], $data)) {
                return $data[$matches[1]];
            }

            return $matches[0];
        };
        $template = preg_replace_callback('/\[\[\s*(.+?)\s*\]\](\r?\n)?/s', $callback, $template);

        return $template;
    }

    protected function renderForeachs($template, $data)
    {
        $callback = function ($matches) use($data) {
            $rep = $matches[0];
            $rep = preg_replace('/\[\[\s*foreach:\s*(.+?)\s*\]\](\r?\n)?/s', '', $rep);
            $rep = preg_replace('/\[\[\s*endforeach\s*\]\](\r?\n)?/s', '', $rep);
            $ret = '';

            if(array_key_exists($matches[1], $data) && is_array($data[$matches[1]])) {
                $parent = $data[$matches[1]];

                foreach ($parent as $i) {
                    $d = [];

                    if(is_array($i)) {

                        foreach ($i as $key => $value) {
                            $d['i.'.$key] = $value;
                        }
                    }
                    else {
                        $d['i'] = $i;
                    }
                    $rep2 = $this->renderIFs($rep, array_merge($d, $data));
                    $rep2 = $this->renderVariables($rep2, array_merge($d, $data));
                    $ret .= $rep2;
                }
                return $ret;
            }
            else {
                return $mat;    
            }
        };
        $template = preg_replace_callback('/\[\[\s*foreach:\s*(.+?)\s*\]\](\r?\n)?((?!endforeach).)*\[\[\s*endforeach\s*\]\](\r?\n)?/s', $callback, $template);

        return $template;
    }

    protected function getValFromExpression($exp, $data)
    {
        if(str_contains($exp, "'")) {
            return trim($exp,"'");    
        }
        else {

            if(array_key_exists($exp, $data)) {
                return $data[$exp];
            }
            else return null;
        }
    }

    protected function renderIFs($template, $data)
    {
        $callback = function ($matches) use($data) {
            $rep = $matches[0];
            $rep = preg_replace('/\[\[\s*if:\s*(.+?)\s*([!=]=)\s*(.+?)\s*\]\](\r?\n)?/s', '', $rep);
            $rep = preg_replace('/\[\[\s*endif\s*\]\](\r?\n)?/s', '', $rep);
            $ret = '';
            $val1 = $this->getValFromExpression($matches[1], $data);
            $val2 = $this->getValFromExpression($matches[3], $data);

            if($matches[2] == '==' && $val1 == $val2) { $ret .= $rep; }
            if($matches[2] == '!=' && $val1 != $val2) { $ret .= $rep; }
            
            return $ret;
        };
        $template = preg_replace_callback('/\[\[\s*if:\s*(.+?)\s*([!=]=)\s*(.+?)\s*\]\](\r?\n)?((?!endif).)*\[\[\s*endif\s*\]\](\r?\n)?/s', $callback, $template);

        return $template;
    }

    protected function customTemplateOfDefault($template_name)
    {
        $trypath = base_path().'/resources/templates/'.$template_name.'.tpl.php';

        if(file_exists($trypath)) return $trypath;

        return __DIR__.'/Templates/'.$template_name.'.tpl.php';
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
        $this->output = $output;
    }

    /**
     * Get output.
     *
     * @return null
     */
    public function getOutputAttribute()
    {
        return $this->output;
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
}
