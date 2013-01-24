<?php

namespace Browscap;

/**
 * Represents a browser
 */
class Browser
{

    public $master_parent            = true;
    public $lite_mode                = true;
    public $parent                   = 'DefaultProperties';
    public $comment                  = 'DefaultProperties';
    public $browser                  = 'DefaultProperties';
    public $version                  = 0.0;
    public $major_version            = 0;
    public $minor_version            = 0;
    public $platform_version         = 0.0;
    public $alpha                    = false;
    public $beta                     = false;
    public $win16                    = false;
    public $win32                    = false;
    public $win64                    = false;
    public $frames                   = false;
    public $iframes                  = false;
    public $tables                   = false;
    public $cookies                  = false;
    public $background_sounds        = false;
    public $javascript               = false;
    public $vbscript                 = false;
    public $java_applets             = false;
    public $activex_controls         = false;
    public $is_mobile_device         = false;
    public $is_syndication_reader    = false;
    public $crawler                  = false;
    public $css_version              = 0.0;
    public $aol_version              = 0.0;
    public $rendering_engine_version = 0.0;

    public $agent_id;
    public $name;
    public $x_parent;
    public $platform;
    public $platform_description;
    public $device_name;
    public $device_maker;
    public $rendering_engine_name;
    public $rendering_engine_description;

    public function setData(array $data)
    {
        $this->agent_id                     = $data['AgentID'];
        $this->name                         = $data['PropertyName'];
        $this->master_parent                = $data['MasterParent'];
        $this->lite_mode                    = $data['LiteMode'];
        $this->parent                       = $data['Parent'];
        $this->x_parent                     = isset($data['X_Parent']) ? $data['X_Parent'] : $this->x_parent;
        $this->comment                      = isset($data['Comment']) ? $data['Comment'] : $this->comment;
        $this->browser                      = $data['Browser'];
        $this->version                      = $data['Version'];
        $this->major_version                = $data['MajorVer'];
        $this->minor_version                = isset($data['MinorVer']) ? $data['MinorVer'] : $this->minor_version;
        $this->platform                     = $data['Platform'];
        $this->platform_version             = $data['Platform_Version'];
        $this->platform_description         = $data['Platform_Description'];
        $this->alpha                        = $data['Alpha'];
        $this->beta                         = $data['Beta'];
        $this->win16                        = $data['Win16'];
        $this->win32                        = $data['Win32'];
        $this->win64                        = $data['Win64'];
        $this->frames                       = $data['Frames'];
        $this->iframes                      = $data['IFrames'];
        $this->tables                       = $data['Tables'];
        $this->cookies                      = $data['Cookies'];
        $this->background_sounds            = $data['BackgroundSounds'];
        $this->javascript                   = $data['JavaScript'];
        $this->vbscript                     = $data['VBScript'];
        $this->java_applets                 = $data['JavaApplets'];
        $this->activex_controls             = $data['ActiveXControls'];
        $this->is_mobile_device             = $data['isMobileDevice'];
        $this->is_syndication_reader        = $data['isSyndicationReader'];
        $this->crawler                      = $data['Crawler'];
        $this->css_version                  = $data['CssVersion'];
        $this->aol_version                  = $data['AolVersion'];
        $this->device_name                  = $data['Device_Name'];
        $this->device_maker                 = $data['Device_Maker'];
        $this->rendering_engine_name        = $data['RenderingEngine_Name'];
        $this->rendering_engine_version     = $data['RenderingEngine_Version'];
        $this->rendering_engine_description = $data['RenderingEngine_Description'];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        return array(
            'agent_id'                     => $this->agent_id,
            'name'                         => $this->name,
            'master_parent'                => $this->master_parent,
            'lite_mode'                    => $this->lite_mode,
            'parent'                       => $this->parent,
            'x_parent'                     => $this->x_parent,
            'comment'                      => $this->comment,
            'browser'                      => $this->browser,
            'version'                      => $this->version,
            'major_version'                => $this->major_version,
            'minor_version'                => $this->minor_version,
            'platform'                     => $this->platform,
            'platform_version'             => $this->platform_version,
            'platform_description'         => $this->platform_description,
            'alpha'                        => $this->alpha,
            'beta'                         => $this->beta,
            'win16'                        => $this->win16,
            'win32'                        => $this->win32,
            'win64'                        => $this->win64,
            'frames'                       => $this->frames,
            'iframes'                      => $this->iframes,
            'tables'                       => $this->tables,
            'cookies'                      => $this->cookies,
            'background_sounds'            => $this->background_sounds,
            'javascript'                   => $this->javascript,
            'vbscript'                     => $this->vbscript,
            'java_applets'                 => $this->java_applets,
            'activex_controls'             => $this->activex_controls,
            'is_mobile_device'             => $this->is_mobile_device,
            'is_syndication_reader'        => $this->is_syndication_reader,
            'crawler'                      => $this->crawler,
            'css_version'                  => $this->css_version,
            'aol_version'                  => $this->aol_version,
            'device_name'                  => $this->device_name,
            'device_maker'                 => $this->device_maker,
            'rendering_engine_name'        => $this->rendering_engine_name,
            'rendering_engine_version'     => $this->rendering_engine_version,
            'rendering_engine_description' => $this->rendering_engine_description,
        );
    }

    public function toXml()
    {
    }

    public function toIni()
    {
        $ini = sprintf("[%s]\n",$this->name);
        foreach ($this->toArray() as $k => $v) {
            $ini .= sprintf("%s=%s\n",$k,(string)$v);
        }
        return $ini;
    }

}
